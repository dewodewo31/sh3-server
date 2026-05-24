<?php

namespace App\Services\OrderService;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrderService implements OrderServiceInterface
{
    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get orders with filters and pagination
     */
    public function getOrders(Request $request): LengthAwarePaginator
    {
        $user = Auth::user();
        $query = Order::with(['participant', 'event', 'payment']);

        // ORGANIZER: Only see orders from their events
        if ($user->role === 'organizer') {
            $eventIds = Event::where('created_by', $user->id)->pluck('id');
            $query->whereIn('event_id', $eventIds);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by event
        if ($request->filled('event_id')) {
            if ($user->role === 'organizer') {
                $event = Event::where('id', $request->event_id)
                    ->where('created_by', $user->id)
                    ->first();
                if ($event) {
                    $query->where('event_id', $request->event_id);
                }
            } else {
                $query->where('event_id', $request->event_id);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('ticket_code', 'like', "%{$search}%")
                  ->orWhereHas('participant', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('hash_id', 'like', "%{$search}%");
                  });
            });
        }

        // Month filter
        $this->applyMonthFilter($query, $request->month);

        return $query->latest()->paginate($request->per_page ?? 15);
    }

    /**
     * Get order by ID
     */
    public function getOrderById($id)
    {
        return Order::with(['participant', 'event', 'payment.verifier'])->findOrFail($id);
    }

    /**
     * Get order statistics
     */
    public function getOrderStats(Request $request): array
    {
        $user = Auth::user();
        
        if ($user->role === 'organizer') {
            $eventIds = Event::where('created_by', $user->id)->pluck('id');
            $query = Order::whereIn('event_id', $eventIds);
        } else {
            $query = Order::query();
        }

        // Apply filters to stats
        $this->applyStatsFilters($query, $request);

        return [
            'totalOrders' => $query->count(),
            'pendingOrders' => (clone $query)->where('status', 'pending')->count(),
            'paidOrders' => (clone $query)->where('status', 'paid')->count(),
            'freeOrders' => (clone $query)->where('status', 'free')->count(),
            'cancelledOrders' => (clone $query)->where('status', 'cancelled')->count(),
            'totalRevenue' => (clone $query)->where('status', 'paid')->sum('total_price'),
        ];
    }

    /**
     * Get filter data (events, statuses)
     */
    public function getFilterData(): array
    {
        $user = Auth::user();
        
        if ($user->role === 'organizer') {
            $events = Event::where('created_by', $user->id)->get();
        } else {
            $events = Event::all();
        }
        
        $statuses = ['pending', 'paid', 'free', 'cancelled'];
        
        return compact('events', 'statuses');
    }

    /**
     * Verify payment
     */
    public function verifyPayment($orderId, array $data)
    {
        $order = Order::findOrFail($orderId);
        
        $order->status = $data['status'];
        $order->save();
        
        if ($order->payment) {
            $order->payment->status = $data['status'] == 'paid' ? 'confirmed' : 'rejected';
            $order->payment->verified_by = Auth::id();
            $order->payment->verified_at = now();
            $order->payment->notes = $data['notes'] ?? null;
            $order->payment->save();
        }
        
        return $order;
    }

    /**
     * Update payment proof from customer
     */
    public function updatePaymentProof($orderId, array $data)
    {
        $order = Order::findOrFail($orderId);
        
        if (in_array($order->status, ['paid', 'cancelled', 'free'])) {
            throw new \Exception('Order sudah diproses, tidak dapat upload bukti pembayaran');
        }
        
        // If free event
        if ($order->event->price <= 0) {
            $order->status = 'free';
            $order->save();
            return $order;
        }
        
        if ($data['amount'] < $order->total_price) {
            throw new \Exception('Jumlah pembayaran kurang dari total harga');
        }
        
        // Upload proof image
        $proofPath = $data['payment_proof']->store('payment-proofs', 'public');
        
        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'payment_method' => $data['payment_method'],
                'payment_proof' => $proofPath,
                'amount' => $data['amount'],
                'paid_at' => $data['paid_at'],
                'status' => 'pending',
                'notes' => 'Menunggu verifikasi admin'
            ]
        );
        
        $order->status = 'pending';
        $order->save();
        
        return $order;
    }

    /**
     * Cancel order
     */
    public function cancelOrder($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        if (in_array($order->status, ['paid', 'free'])) {
            throw new \Exception('Order yang sudah dikonfirmasi tidak dapat dibatalkan');
        }
        
        $order->status = 'cancelled';
        $order->save();
        
        if ($order->payment) {
            $order->payment->status = 'rejected';
            $order->payment->verified_by = Auth::id();
            $order->payment->verified_at = now();
            $order->payment->notes = 'Order dibatalkan oleh ' . Auth::user()->role;
            $order->payment->save();
        }
        
        return $order;
    }

    /**
     * Delete order (Admin only)
     */
    public function deleteOrder($orderId)
    {
        $order = Order::findOrFail($orderId);
        
        if ($order->payment && $order->payment->payment_proof) {
            Storage::disk('public')->delete($order->payment->payment_proof);
        }
        
        if ($order->payment) {
            $order->payment->delete();
        }
        
        return $order->delete();
    }

    /**
     * Check ticket by code (public)
     */
    public function checkTicket(string $ticketCode): ?array
    {
        $order = Order::with(['event', 'participant'])
            ->where('ticket_code', $ticketCode)
            ->first();
        
        if (!$order) {
            return null;
        }
        
        return [
            'ticket_code' => $order->ticket_code,
            'invoice_number' => $order->invoice_number,
            'event_name' => $order->event->title,
            'event_date' => $order->event->start_date->format('d M Y'),
            'event_location' => $order->event->location,
            'participant_name' => $order->participant->name,
            'status' => $order->status,
            'valid' => in_array($order->status, ['paid', 'free'])
        ];
    }

    /**
     * Export orders to CSV
     */
    public function exportOrdersToCsv(Request $request)
    {
        $user = Auth::user();
        $query = Order::with(['participant', 'event']);
        
        if ($user->role === 'organizer') {
            $eventIds = Event::where('created_by', $user->id)->pluck('id');
            $query->whereIn('event_id', $eventIds);
        }
        
        $this->applyExportFilters($query, $request);
        
        $orders = $query->get();
        
        $filename = 'orders_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w+');
        
        // Headers
        fputcsv($handle, [
            'Invoice Number', 'Ticket Code', 'Participant Name', 'Participant Email',
            'Participant Hash ID', 'Event Title', 'Event Date', 'Total Price', 'Status', 'Order Date'
        ]);
        
        foreach ($orders as $order) {
            fputcsv($handle, [
                $order->invoice_number,
                $order->ticket_code,
                $order->participant->name ?? 'N/A',
                $order->participant->email ?? 'N/A',
                $order->participant->hash_id ?? 'N/A',
                $order->event->title ?? 'N/A',
                $order->event->start_date->format('d/m/Y') ?? 'N/A',
                $order->total_price > 0 ? 'Rp ' . number_format($order->total_price, 0, ',', '.') : 'FREE',
                $order->status,
                $order->created_at->format('d/m/Y H:i')
            ]);
        }
        
        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);
        
        return [
            'content' => $csvContent,
            'filename' => $filename,
            'headers' => [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        ];
    }

    /**
     * Export all orders to PDF
     */
    public function exportOrdersToPdf(Request $request)
    {
        $user = Auth::user();
        $query = Order::with(['participant', 'event', 'payment']);
        
        if ($user->role === 'organizer') {
            $eventIds = Event::where('created_by', $user->id)->pluck('id');
            $query->whereIn('event_id', $eventIds);
        }
        
        $this->applyExportFilters($query, $request);
        
        $orders = $query->latest()->get();
        
        $stats = [
            'totalOrders' => $orders->count(),
            'totalRevenue' => $orders->where('status', 'paid')->sum('total_price'),
            'pendingOrders' => $orders->where('status', 'pending')->count(),
            'paidOrders' => $orders->where('status', 'paid')->count(),
            'freeOrders' => $orders->where('status', 'free')->count(),
            'cancelledOrders' => $orders->where('status', 'cancelled')->count(),
        ];
        
        $pdf = Pdf::loadView('exports.orders-all', array_merge(compact('orders'), $stats));
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('orders_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export single order to PDF (Invoice)
     */
    public function exportSingleOrderToPdf($orderId)
    {
        $order = $this->getOrderById($orderId);
        
        $pdf = Pdf::loadView('exports.order-invoice', compact('order'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('invoice_' . $order->invoice_number . '.pdf');
    }

    /**
     * Authorize order access for organizer
     */
    public function authorizeOrder($orderId): bool
    {
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            return true;
        }
        
        if ($user->role === 'organizer') {
            $order = Order::findOrFail($orderId);
            $event = Event::find($order->event_id);
            return $event && $event->created_by === $user->id;
        }
        
        return false;
    }

    /**
     * Apply month filter to query
     */
    private function applyMonthFilter($query, $monthYear): void
    {
        if (!$monthYear) return;
        
        try {
            if (preg_match('/^\d{4}-\d{2}$/', $monthYear)) {
                $parts = explode('-', $monthYear);
                $year = (int) $parts[0];
                $month = (int) $parts[1];
                
                if ($month >= 1 && $month <= 12) {
                    $startDate = Carbon::create($year, $month, 1)->startOfMonth();
                    $endDate = Carbon::create($year, $month, 1)->endOfMonth();
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Filter bulan error: ' . $e->getMessage());
        }
    }

    /**
     * Apply filters for stats
     */
    private function applyStatsFilters($query, Request $request): void
    {
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }
        
        $this->applyMonthFilter($query, $request->month);
    }

    /**
     * Apply filters for export
     */
    private function applyExportFilters($query, Request $request): void
    {
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }
        
        $this->applyMonthFilter($query, $request->month);
    }
}