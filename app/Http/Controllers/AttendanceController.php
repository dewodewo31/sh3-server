<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceController extends Controller
{
    /**
     * Show QR Code for event attendance
     */
    public function showQrCode($eventId)
    {
        $user = Auth::user();
        
        // Cari participant berdasarkan user (jika menggunakan user)
        // Atau sesuaikan dengan logic participant Anda
        $participant = $user->participant ?? null;
        
        if (!$participant) {
            return redirect()->back()->with('error', 'Participant not found');
        }
        
        // Cari order untuk event ini
        $order = $participant->orders()
            ->where('event_id', $eventId)
            ->whereIn('status', ['paid', 'free'])
            ->first();
        
        if (!$order) {
            return redirect()->back()->with('error', 'You are not registered for this event');
        }
        
        // Cari atau buat attendance
        $attendance = Attendance::firstOrCreate(
            ['order_id' => $order->id],
            [
                'event_id' => $eventId,
                'participant_id' => $participant->id,
                'status' => 'pending'
            ]
        );
        
        $event = Event::findOrFail($eventId);
        
        // Generate QR Code URL
        $qrCodeUrl = route('attendance.scan', $attendance->qr_code);
        
        return view('attendance.qrcode', compact('attendance', 'event', 'qrCodeUrl'));
    }
    
    /**
     * Scan QR Code and process check in/out
     */
    public function scan($qrCode)
    {
        $attendance = Attendance::where('qr_code', $qrCode)->first();
        
        if (!$attendance) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid QR Code'
                ], 404);
            }
            return redirect()->route('attendance.scanner')->with('error', 'Invalid QR Code');
        }
        
        $event = $attendance->event;
        $participant = $attendance->participant;
        
        // ========== VALIDASI EVENT STATUS ==========
        
        // Cek apakah event sudah selesai (finished)
        if ($event->end_date < now()) {
            $message = "Event '{$event->title}' has already ended. Cannot process attendance.";
            $data = [
                'participant_name' => $participant->name,
                'event_name' => $event->title,
                'event_status' => 'finished',
                'message' => 'Event sudah selesai, tidak dapat melakukan absen'
            ];
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => $message, 
                    'data' => $data
                ], 400);
            }
            return redirect()->route('attendance.scanner')->with('error', $message);
        }
        
        // Cek apakah event belum dimulai
        if ($event->start_date > now()) {
            $message = "Event '{$event->title}' hasn't started yet.";
            $data = [
                'participant_name' => $participant->name,
                'event_name' => $event->title,
                'event_status' => 'upcoming',
                'start_date' => $event->start_date->format('d M Y H:i'),
                'message' => "Event akan dimulai pada {$event->start_date->format('d M Y H:i')} WIB"
            ];
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => $message, 
                    'data' => $data
                ], 400);
            }
            return redirect()->route('attendance.scanner')->with('error', $message);
        }
        
        // ========== PROSES CHECK IN / CHECK OUT ==========
        
        // CHECK IN
        if (!$attendance->check_in_time) {
            $attendance->update([
                'check_in_time' => now(),
                'status' => 'checked_in',
                'check_in_ip' => request()->ip()
            ]);
            
            $message = "✅ Check In successful for {$participant->name}";
            $data = [
                'participant_name' => $participant->name,
                'event_name' => $event->title,
                'check_in_time' => $attendance->check_in_time,
                'status' => 'checked_in',
                'can_check_out' => false,
                'remaining_minutes' => 15
            ];
            
            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message, 'data' => $data]);
            }
            return redirect()->route('attendance.scanner')->with('success', $message);
        }
        
        // CHECK OUT
        if (!$attendance->check_out_time) {
            // Validasi minimal 15 menit
            if (!$attendance->canCheckOut()) {
                $remaining = $attendance->getRemainingMinutesBeforeCheckout();
                $message = "⏰ Cannot check out yet. Please wait {$remaining} more minutes";
                
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false, 
                        'message' => $message, 
                        'data' => ['remaining_minutes' => $remaining]
                    ], 400);
                }
                return redirect()->route('attendance.scanner')->with('error', $message);
            }
            
            $attendance->update([
                'check_out_time' => now(),
                'status' => 'checked_out',
                'check_out_ip' => request()->ip()
            ]);
            
            $message = "✅ Check Out successful for {$participant->name}";
            $data = [
                'participant_name' => $participant->name,
                'event_name' => $event->title,
                'check_in_time' => $attendance->check_in_time,
                'check_out_time' => $attendance->check_out_time,
                'status' => 'checked_out'
            ];
            
            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => $message, 'data' => $data]);
            }
            return redirect()->route('attendance.scanner')->with('success', $message);
        }
        
        $message = 'Already checked in and checked out';
        if (request()->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], 400);
        }
        return redirect()->route('attendance.scanner')->with('error', $message);
    }
    
    /**
     * Show scanner page
     */
    public function scanner()
    {
        return view('attendance.scanner');
    }
    
    /**
     * API: Get attendance status for an event (for mobile scanner)
     */
    public function getStatus($qrCode)
    {
        $attendance = Attendance::where('qr_code', $qrCode)
            ->with(['participant', 'event'])
            ->first();
        
        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR Code'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'participant_name' => $attendance->participant->name,
                'event_name' => $attendance->event->title,
                'check_in_time' => $attendance->check_in_time,
                'check_out_time' => $attendance->check_out_time,
                'status' => $attendance->status,
                'can_check_out' => $attendance->canCheckOut(),
                'remaining_minutes' => $attendance->getRemainingMinutesBeforeCheckout()
            ]
        ]);
    }
    
    /**
     * Get attendance list for an event (Admin/Owner)
     */
    public function eventAttendance($eventId)
    {
        $event = Event::findOrFail($eventId);
        
        // Authorize: hanya admin atau owner event
        if (Auth::user()->role !== 'admin_full_access' && $event->created_by !== Auth::id()) {
            abort(403);
        }
        
        $attendances = Attendance::with(['participant', 'order'])
            ->where('event_id', $eventId)
            ->latest()
            ->paginate(50);
        
        $stats = [
            'total' => $attendances->total(),
            'checked_in' => Attendance::where('event_id', $eventId)
                ->whereNotNull('check_in_time')->count(),
            'checked_out' => Attendance::where('event_id', $eventId)
                ->whereNotNull('check_out_time')->count(),
            'pending' => Attendance::where('event_id', $eventId)
                ->whereNull('check_in_time')->count()
        ];
        
        return view('attendance.event-list', compact('event', 'attendances', 'stats'));
    }
    
    /**
     * Export attendance report
     */
    public function export($eventId)
    {
        $event = Event::findOrFail($eventId);
        
        $attendances = Attendance::with(['participant', 'order'])
            ->where('event_id', $eventId)
            ->get();
        
        $filename = 'attendance_' . $event->slug . '_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://temp', 'w+');
        
        fputcsv($handle, [
            'Participant Name',
            'Email',
            'Phone',
            'Check In Time',
            'Check Out Time',
            'Status',
            'Check In IP',
            'Check Out IP'
        ]);
        
        foreach ($attendances as $attendance) {
            fputcsv($handle, [
                $attendance->participant->name,
                $attendance->participant->email,
                $attendance->participant->phone,
                $attendance->check_in_time?->format('d M Y H:i:s'),
                $attendance->check_out_time?->format('d M Y H:i:s'),
                $attendance->status,
                $attendance->check_in_ip,
                $attendance->check_out_ip
            ]);
        }
        
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);
        
        return response($csvContent)
            ->withHeaders([
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
    }
}