<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Order::with(['participant', 'event', 'payment']); // Hapus 'user'
        
        // 🔒 ORGANIZER: Hanya lihat order dari event miliknya
        if ($user->role === 'organizer') {
            $eventIds = Event::where('created_by', $user->id)->pluck('id');
            $query->whereIn('event_id', $eventIds);
        }
        
        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filter by event
        if ($request->has('event_id') && $request->event_id != '') {
            if ($user->role === 'organizer') {
                $event = Event::where('id', $request->event_id)->where('created_by', $user->id)->first();
                if ($event) {
                    $query->where('event_id', $request->event_id);
                }
            } else {
                $query->where('event_id', $request->event_id);
            }
        }
        
        // Search by invoice, ticket code, or participant name/email
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('invoice_number', 'like', '%' . $request->search . '%')
                  ->orWhere('ticket_code', 'like', '%' . $request->search . '%')
                  ->orWhereHas('participant', function($q2) use ($request) {
                      $q2->where('name', 'like', '%' . $request->search . '%')
                         ->orWhere('email', 'like', '%' . $request->search . '%')
                         ->orWhere('hash_id', 'like', '%' . $request->search . '%');
                  });
            });
        }
        
        $orders = $query->latest()->paginate(15);
        
        // For filters
        if ($user->role === 'organizer') {
            $events = Event::where('created_by', $user->id)->get();
        } else {
            $events = Event::all();
        }
        
        $statuses = ['pending', 'paid', 'free', 'cancelled'];
        
        // Stats untuk organizer
        if ($user->role === 'organizer') {
            $eventIds = Event::where('created_by', $user->id)->pluck('id');
            $totalOrders = Order::whereIn('event_id', $eventIds)->count();
            $pendingOrders = Order::whereIn('event_id', $eventIds)->where('status', 'pending')->count();
            $paidOrders = Order::whereIn('event_id', $eventIds)->where('status', 'paid')->count();
            $freeOrders = Order::whereIn('event_id', $eventIds)->where('status', 'free')->count();
            $cancelledOrders = Order::whereIn('event_id', $eventIds)->where('status', 'cancelled')->count();
            $totalRevenue = Order::whereIn('event_id', $eventIds)->where('status', 'paid')->sum('total_price');
        } else {
            $totalOrders = Order::count();
            $pendingOrders = Order::where('status', 'pending')->count();
            $paidOrders = Order::where('status', 'paid')->count();
            $freeOrders = Order::where('status', 'free')->count();
            $cancelledOrders = Order::where('status', 'cancelled')->count();
            $totalRevenue = Order::where('status', 'paid')->sum('total_price');
        }
        
        return view('orders.index', compact('orders', 'events', 'statuses', 
                                            'totalOrders', 'pendingOrders', 
                                            'paidOrders', 'freeOrders', 'cancelledOrders',
                                            'totalRevenue'));
    }

    public function show(Order $order)
    {
        // 🔒 Authorize: Cek apakah organizer bisa melihat order ini
        $this->authorizeOrder($order);
        
        $order->load(['participant', 'event', 'payment.verifier']); // Hapus 'user'
        
        return view('orders.show', compact('order'));
    }

    /**
     * Admin verifikasi pembayaran
     */
    public function verifyPayment(Request $request, Order $order)
    {
        // 🔒 Authorize: Cek apakah organizer bisa verifikasi order ini
        $this->authorizeOrder($order);
        
        $request->validate([
            'status' => 'required|in:paid,cancelled',
            'notes' => 'nullable|string'
        ]);
        
        // Update order status
        $order->status = $request->status;
        $order->save();
        
        // Update payment if exists
        if ($order->payment) {
            $order->payment->status = $request->status == 'paid' ? 'confirmed' : 'rejected';
            $order->payment->verified_by = Auth::id();
            $order->payment->verified_at = now();
            $order->payment->notes = $request->notes;
            $order->payment->save();
        }
        
        $statusText = $request->status == 'paid' ? 'diverifikasi' : 'dibatalkan';
        
        return redirect()->route('orders.show', $order)
            ->with('success', "Pembayaran berhasil {$statusText}");
    }
    
    /**
     * Update payment proof (from customer via API)
     */
    public function updatePaymentProof(Request $request, Order $order)
    {
        // Cek apakah order sudah paid atau cancelled
        if (in_array($order->status, ['paid', 'cancelled', 'free'])) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Order sudah diproses, tidak dapat upload bukti pembayaran');
        }
        
        // Jika event gratis
        if ($order->event->price <= 0) {
            $order->status = 'free';
            $order->save();
            
            return redirect()->route('orders.show', $order)
                ->with('success', 'Event gratis, order berhasil dikonfirmasi');
        }
        
        $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'paid_at' => 'required|date'
        ]);
        
        // Cek apakah amount sesuai
        if ($request->amount < $order->total_price) {
            return redirect()->back()
                ->with('error', 'Jumlah pembayaran kurang dari total harga');
        }
        
        // Upload proof image
        $proofPath = $request->file('payment_proof')->store('payment-proofs', 'public');
        
        // Create or update payment
        Payment::updateOrCreate(
            ['order_id' => $order->id],
            [
                'payment_method' => $request->payment_method,
                'payment_proof' => $proofPath,
                'amount' => $request->amount,
                'paid_at' => $request->paid_at,
                'status' => 'pending',
                'notes' => 'Menunggu verifikasi admin'
            ]
        );
        
        // Order status tetap pending
        $order->status = 'pending';
        $order->save();
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'Bukti pembayaran berhasil diupload, menunggu verifikasi admin');
    }

    /**
     * Delete order (Admin only)
     */
    public function destroy(Order $order)
    {
        // 🔒 Authorize: Hanya admin yang bisa hapus order
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only admin can delete orders');
        }
        
        // Delete payment proof if exists
        if ($order->payment && $order->payment->payment_proof) {
            Storage::disk('public')->delete($order->payment->payment_proof);
        }
        
        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Order berhasil dihapus');
    }
    
    /**
     * Cancel order (Admin atau Organizer)
     */
    public function cancel(Order $order)
    {
        // 🔒 Authorize: Cek apakah organizer bisa cancel order ini
        $this->authorizeOrder($order);
        
        // Cek apakah order sudah paid/free
        if (in_array($order->status, ['paid', 'free'])) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Order yang sudah dikonfirmasi tidak dapat dibatalkan');
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
        
        return redirect()->route('orders.show', $order)
            ->with('success', 'Order berhasil dibatalkan');
    }
    
    /**
     * 🔒 Authorization helper untuk Order
     */
    private function authorizeOrder(Order $order)
    {
        $user = Auth::user();
        
        // Admin bisa akses semua
        if ($user->role === 'admin') {
            return;
        }
        
        // Organizer hanya bisa akses order dari event miliknya
        if ($user->role === 'organizer') {
            $event = Event::find($order->event_id);
            if (!$event || $event->created_by !== $user->id) {
                abort(403, 'Tidak diizinkan mengakses order ini');
            }
            return;
        }
        
        abort(403, 'Unauthorized');
    }
    
    /**
     * Get order by ticket code (public API)
     */
    public function checkTicket(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required|string'
        ]);
        
        $order = Order::with(['event', 'participant'])
            ->where('ticket_code', $request->ticket_code)
            ->first();
        
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'ticket_code' => $order->ticket_code,
                'invoice_number' => $order->invoice_number,
                'event_name' => $order->event->title,
                'event_date' => $order->event->start_date->format('d M Y'),
                'event_location' => $order->event->location,
                'participant_name' => $order->participant->name,
                'status' => $order->status,
                'valid' => in_array($order->status, ['paid', 'free'])
            ]
        ]);
    }
    
    /**
     * Get orders by participant (API)
     */
    public function participantOrders(Request $request)
    {
        $participant = $request->user();
        
        $orders = Order::with(['event', 'payment'])
            ->where('participant_id', $participant->id)
            ->latest()
            ->paginate(15);
        
        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }
    
    /**
     * Export orders to CSV
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $query = Order::with(['participant', 'event']);
        
        // 🔒 Organizer hanya export order dari event miliknya
        if ($user->role === 'organizer') {
            $eventIds = Event::where('created_by', $user->id)->pluck('id');
            $query->whereIn('event_id', $eventIds);
        }
        
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('event_id') && $request->event_id != '') {
            if ($user->role === 'organizer') {
                $event = Event::where('id', $request->event_id)->where('created_by', $user->id)->first();
                if ($event) {
                    $query->where('event_id', $request->event_id);
                }
            } else {
                $query->where('event_id', $request->event_id);
            }
        }
        
        $orders = $query->get();
        
        $filename = 'orders_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w+');
        
        // Add CSV headers
        fputcsv($handle, [
            'Invoice Number',
            'Ticket Code',
            'Participant Name',
            'Participant Email',
            'Participant Hash ID',
            'Event Title',
            'Event Date',
            'Total Price',
            'Status',
            'Order Date'
        ]);
        
        // Add data rows
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
        
        return response($csvContent)
            ->withHeaders([
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
    }
}