<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use App\Models\Attendance;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ParticipantAttendanceController extends Controller
{
    /**
     * Get all events that participant has registered for
     * GET /api/v1/participant/events
     */
    public function myEvents(Request $request)
    {
        $participant = $request->user();
        
        $orders = Order::with(['event' => function($query) {
            $query->with(['category', 'creator']);
        }])
        ->where('participant_id', $participant->id)
        ->whereIn('status', ['paid', 'free'])
        ->latest()
        ->get();
        
        $events = $orders->map(function($order) {
            $event = $order->event;
            $attendance = $order->attendance;
            
            return [
                'id' => $event->id,
                'title' => $event->title,
                'slug' => $event->slug,
                'description' => $event->description,
                'location' => $event->location,
                'start_date' => $event->start_date->toISOString(),
                'end_date' => $event->end_date->toISOString(),
                'price' => $event->price,
                'status' => $event->status,
                'order_status' => $order->status,
                'ticket_code' => $order->ticket_code,
                'invoice_number' => $order->invoice_number,
                'attendance' => [
                    'qr_code' => $attendance ? $attendance->qr_code : null,
                    'check_in_time' => $attendance?->check_in_time?->toISOString(),
                    'check_out_time' => $attendance?->check_out_time?->toISOString(),
                    'attendance_status' => $attendance?->status ?? 'pending'
                ],
                'image_url' => $event->image ? asset('storage/' . $event->image) : null,
                'category' => $event->category ? [
                    'id' => $event->category->id,
                    'name' => $event->category->name
                ] : null,
                'created_at' => $event->created_at->toISOString()
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $events,
            'total' => $events->count()
        ]);
    }
    
    /**
     * Get specific event details with QR code
     * GET /api/v1/participant/events/{eventId}
     */
    public function eventDetail(Request $request, $eventId)
    {
        $participant = $request->user();
        
        $order = Order::with(['event', 'attendance'])
            ->where('participant_id', $participant->id)
            ->where('event_id', $eventId)
            ->whereIn('status', ['paid', 'free'])   
            ->first();
        
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'You are not registered for this event'
            ], 404);
        }
        
        $event = $order->event;
        $attendance = $order->attendance;
        
        // Generate QR Code as base64
        $qrCodeBase64 = null;
        if ($attendance) {
            $qrCodeUrl = route('attendance.scan', $attendance->qr_code);
            $qrCodeBase64 = base64_encode(QrCode::format('png')->size(300)->generate($qrCodeUrl));
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'event' => [
                    'id' => $event->id,
                    'title' => $event->title,
                    'slug' => $event->slug,
                    'description' => $event->description,
                    'location' => $event->location,
                    'latitude' => $event->latitude,
                    'longitude' => $event->longitude,
                    'start_date' => $event->start_date->toISOString(),
                    'end_date' => $event->end_date->toISOString(),
                    'price' => $event->price,
                    'quota' => $event->quota,
                    'registered_count' => $event->orders()->count(),
                    'status' => $event->status,
                    'image_url' => $event->image ? asset('storage/' . $event->image) : null,
                    'category' => $event->category ? [
                        'id' => $event->category->id,
                        'name' => $event->category->name
                    ] : null,
                    'organizer' => [
                        'id' => $event->creator->id,
                        'name' => $event->creator->name
                    ]
                ],
                'order' => [
                    'id' => $order->id,
                    'invoice_number' => $order->invoice_number,
                    'ticket_code' => $order->ticket_code,
                    'total_price' => $order->total_price,
                    'status' => $order->status,
                    'created_at' => $order->created_at->toISOString()
                ],
                'attendance' => [
                    'qr_code' => $attendance ? $attendance->qr_code : null,
                    'qr_code_image' => $qrCodeBase64,
                    'check_in_time' => $attendance?->check_in_time?->toISOString(),
                    'check_out_time' => $attendance?->check_out_time?->toISOString(),
                    'status' => $attendance?->status ?? 'pending',
                    'can_check_out' => $attendance ? $attendance->canCheckOut() : false,
                    'remaining_minutes' => $attendance ? $attendance->getRemainingMinutesBeforeCheckout() : 0
                ]
            ]
        ]);
    }
    
    /**
     * Get QR Code image for event (as PNG)
     * GET /api/v1/participant/events/{eventId}/qrcode
     */
    public function getQrCodeImage(Request $request, $eventId)
    {
        $participant = $request->user();
        
        $order = Order::with(['attendance'])
            ->where('participant_id', $participant->id)
            ->where('event_id', $eventId)
            ->whereIn('status', ['paid', 'free'])
            ->first();
        
        if (!$order || !$order->attendance) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code not found'
            ], 404);
        }
        
        $attendance = $order->attendance;
        $qrCodeUrl = route('attendance.scan', $attendance->qr_code);
        
        $qrCode = QrCode::format('png')->size(400)->generate($qrCodeUrl);
        
        return response($qrCode)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'inline; filename="qrcode_event_' . $eventId . '.png"');
    }
    
    /**
     * Get QR Code as base64 string
     * GET /api/v1/participant/events/{eventId}/qrcode/base64
     */
    public function getQrCodeBase64(Request $request, $eventId)
    {
        $participant = $request->user();
        
        $order = Order::with(['attendance'])
            ->where('participant_id', $participant->id)
            ->where('event_id', $eventId)
            ->whereIn('status', ['paid', 'free'])
            ->first();
        
        if (!$order || !$order->attendance) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code not found'
            ], 404);
        }
        
        $attendance = $order->attendance;
        $qrCodeUrl = route('attendance.scan', $attendance->qr_code);
        
        $qrCodeBase64 = base64_encode(QrCode::format('png')->size(300)->generate($qrCodeUrl));
        
        return response()->json([
            'success' => true,
            'data' => [
                'qr_code' => $attendance->qr_code,
                'qr_code_base64' => 'data:image/png;base64,' . $qrCodeBase64,
                'qr_code_url' => route('attendance.scan', $attendance->qr_code)
            ]
        ]);
    }
    
    /**
     * Get attendance status for an event
     * GET /api/v1/participant/events/{eventId}/attendance-status
     */
    public function getAttendanceStatus(Request $request, $eventId)
    {
        $participant = $request->user();
        
        $order = Order::with(['attendance'])
            ->where('participant_id', $participant->id)
            ->where('event_id', $eventId)
            ->whereIn('status', ['paid', 'free'])
            ->first();
        
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'You are not registered for this event'
            ], 404);
        }
        
        $attendance = $order->attendance;
        
        return response()->json([
            'success' => true,
            'data' => [
                'event_id' => $eventId,
                'event_title' => $order->event->title,
                'has_attendance' => !is_null($attendance),
                'qr_code' => $attendance ? $attendance->qr_code : null,
                'check_in_time' => $attendance?->check_in_time?->toISOString(), 
                'check_out_time' => $attendance?->check_out_time?->toISOString(),
                'status' => $attendance?->status ?? 'pending',
                'can_check_out' => $attendance ? $attendance->canCheckOut() : false,
                'remaining_minutes' => $attendance ? $attendance->getRemainingMinutesBeforeCheckout() : 0,
                'is_event_active' => $order->event->start_date <= now() && $order->event->end_date >= now(),
                'is_event_upcoming' => $order->event->start_date > now(),
                'is_event_finished' => $order->event->end_date < now()
            ]
        ]);
    }
    
    /**
     * Get all attendance history for participant
     * GET /api/v1/participant/attendance-history
     */
    public function attendanceHistory(Request $request)
    {
        $participant = $request->user();
        
        $attendances = Attendance::with(['event', 'order'])
            ->where('participant_id', $participant->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $data = $attendances->map(function($attendance) {
            return [
                'id' => $attendance->id,
                'event' => [
                    'id' => $attendance->event->id,
                    'title' => $attendance->event->title,
                    'start_date' => $attendance->event->start_date->toISOString(),
                    'end_date' => $attendance->event->end_date->toISOString()
                ],
                'qr_code' => $attendance->qr_code,
                'check_in_time' => $attendance->check_in_time?->toISOString(),
                'check_out_time' => $attendance->check_out_time?->toISOString(),
                'status' => $attendance->status,
                'created_at' => $attendance->created_at->toISOString()
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'current_page' => $attendances->currentPage(),
                'last_page' => $attendances->lastPage(),
                'per_page' => $attendances->perPage(),
                'total' => $attendances->total()
            ]
        ]);
    }

    /**
     * Universal Scanner API untuk Aplikasi Mobile (Flutter)
     * Bisa dipanggil via GET /scan/{qrCode} atau POST /check-in dengan Body {"qr_code": "..."}
     */
    public function scan(Request $request, $qrCode = null)
    {
        // 1. Tangkap input (Prioritaskan dari parameter URL, jika kosong ambil dari Body JSON)
        $rawInput = $qrCode ?? $request->input('qr_code') ?? $request->input('member_id');

        if (!$rawInput) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code atau Member ID wajib dikirimkan'
            ], 400);
        }

        // 2. Bersihkan input jika yang ter-scan adalah Full URL
        $cleanCode = trim($rawInput);
        if (str_starts_with($cleanCode, 'http')) {
            $pathSegments = explode('/', parse_url($cleanCode, PHP_URL_PATH));
            $cleanCode = end($pathSegments);
        }

        // 3. Cari attendance berdasarkan kode murni atau Hash ID Pelari
        $attendance = Attendance::where('qr_code', $cleanCode)
            ->orWhereHas('participant', function ($q) use ($cleanCode) {
                $q->where('hash_id', $cleanCode);
            })->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket tidak ditemukan atau belum dibayar lunas'
            ], 404);
        }

        $event = $attendance->event;
        $participant = $attendance->participant;

        // ========== VALIDASI JADWAL EVENT ==========

        if ($event->end_date < now()) {
            return response()->json([
                'success' => false,
                'message' => "Event '{$event->title}' sudah selesai.",
                'data' => [
                    'participant_name' => $participant->name,
                    'event_status' => 'finished'
                ]
            ], 400);
        }

        if ($event->start_date > now()) {
            return response()->json([
                'success' => false,
                'message' => "Event belum dimulai. Jadwal: {$event->start_date->format('d M Y H:i')} WIB",
                'data' => [
                    'participant_name' => $participant->name,
                    'event_status' => 'upcoming'
                ]
            ], 400);
        }

        // ========== PROSES CHECK-IN & CHECK-OUT ==========

        // SKENARIO A: CHECK-IN (POS BERANGKAT)
        if (!$attendance->check_in_time) {
            $attendance->update([
                'check_in_time' => now(),
                'status' => 'checked_in',
                'check_in_ip' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => "BERHASIL CHECK-IN: {$participant->name}",
                'data' => [
                    'participant_name' => $participant->name,
                    'hash_id' => $participant->hash_id ?? '-',
                    'event_name' => $event->title,
                    'check_in_time' => $attendance->check_in_time->toISOString(),
                    'status' => 'checked_in'
                ]
            ], 200);
        }

        // SKENARIO B: CHECK-OUT (GARIS FINISH)
        if (!$attendance->check_out_time) {
            if (!$attendance->canCheckOut()) {
                $remaining = $attendance->getRemainingMinutesBeforeCheckout();
                return response()->json([
                    'success' => false,
                    'message' => "Pelari baru bisa check-out dalam {$remaining} menit lagi.",
                    'data' => ['remaining_minutes' => $remaining]
                ], 400);
            }

            $attendance->update([
                'check_out_time' => now(),
                'status' => 'checked_out',
                'check_out_ip' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => "FINISH! CHECK-OUT: {$participant->name}",
                'data' => [
                    'participant_name' => $participant->name,
                    'hash_id' => $participant->hash_id ?? '-',
                    'event_name' => $event->title,
                    'check_in_time' => $attendance->check_in_time->toISOString(),
                    'check_out_time' => $attendance->check_out_time->toISOString(),
                    'status' => 'checked_out'
                ]
            ], 200);
        }

        // SKENARIO C: SUDAH SELESAI SEMUA
        return response()->json([
            'success' => false,
            'message' => "Peserta {$participant->name} sudah menyelesaikan trek ini sebelumnya."
        ], 400);
    }
}