<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Payment;
use App\Models\Order;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
     public function index(Request $request)
    {
        $user = Auth::user();
        $query = Payment::with(['order', 'order.participant', 'order.event']);
        
        // 🔒 ORGANIZER: Hanya lihat payment dari event miliknya
        if ($user->role === 'organizer') {
            $eventIds = Event::where('created_by', $user->id)->pluck('id');
            $query->whereHas('order', function($q) use ($eventIds) {
                $q->whereIn('event_id', $eventIds);
            });
        }
        
        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filter by payment method
        if ($request->has('payment_method') && $request->payment_method != '') {
            $query->where('payment_method', $request->payment_method);
        }
        
        // Search by invoice or customer name
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->whereHas('order', function($q2) use ($request) {
                    $q2->where('invoice_number', 'like', '%' . $request->search . '%')
                       ->orWhereHas('participant', function($q3) use ($request) {
                           $q3->where('name', 'like', '%' . $request->search . '%')
                              ->orWhere('email', 'like', '%' . $request->search . '%');
                       });
                });
            });
        }
        
        $payments = $query->latest()->paginate(15);
        
        // Stats untuk organizer
        if ($user->role === 'organizer') {
            $eventIds = Event::where('created_by', $user->id)->pluck('id');
            $totalPayments = Payment::whereHas('order', function($q) use ($eventIds) {
                $q->whereIn('event_id', $eventIds);
            })->count();
            $pendingPayments = Payment::whereHas('order', function($q) use ($eventIds) {
                $q->whereIn('event_id', $eventIds);
            })->where('status', 'pending')->count();
            $confirmedPayments = Payment::whereHas('order', function($q) use ($eventIds) {
                $q->whereIn('event_id', $eventIds);
            })->where('status', 'confirmed')->count();
            $rejectedPayments = Payment::whereHas('order', function($q) use ($eventIds) {
                $q->whereIn('event_id', $eventIds);
            })->where('status', 'rejected')->count();
            $totalAmount = Payment::whereHas('order', function($q) use ($eventIds) {
                $q->whereIn('event_id', $eventIds);
            })->where('status', 'confirmed')->sum('amount');
        } else {
            $totalPayments = Payment::count();
            $pendingPayments = Payment::where('status', 'pending')->count();
            $confirmedPayments = Payment::where('status', 'confirmed')->count();
            $rejectedPayments = Payment::where('status', 'rejected')->count();
            $totalAmount = Payment::where('status', 'confirmed')->sum('amount');
        }
        
        $paymentMethods = ['Bank Transfer BCA', 'Bank Transfer Mandiri', 'Bank Transfer BRI', 'QRIS', 'DANA', 'OVO', 'GoPay'];
        $statuses = ['pending', 'confirmed', 'rejected'];
        
        return view('payments.index', compact('payments', 'totalPayments', 'pendingPayments', 
                                            'confirmedPayments', 'rejectedPayments', 'totalAmount',
                                            'paymentMethods', 'statuses'));
    }

    public function show(Payment $payment)
    {
        $payment->load('order.participant', 'order.event', 'verifier');

        return view('payments.show', [
            'payment' => $payment,
            'order' => $payment->order
        ]);
    }

    public function store(StorePaymentRequest $request)
    {
        $data = $request->validated();

        // Upload bukti pembayaran
        if ($request->hasFile('payment_proof')) {
            $data['payment_proof'] = $request->file('payment_proof')
                ->store('payments', 'public');
        }
        
        $data['status'] = 'pending';
        $data['paid_at'] = now();

        $payment = Payment::create($data);
        
        // Update order status to pending (waiting verification)
        if ($payment->order) {
            $payment->order->update(['status' => 'pending']);
        }

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Payment proof has been uploaded. Waiting for admin verification.');
    }

    public function update(UpdatePaymentRequest $request, Payment $payment)
    {
        // 🔒 Authorize: Hanya admin yang bisa update payment
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only admin can update payment');
        }
        
        $data = $request->validated();

        if ($request->hasFile('payment_proof')) {
            if ($payment->payment_proof) {
                Storage::disk('public')->delete($payment->payment_proof);
            }

            $data['payment_proof'] = $request->file('payment_proof')
                ->store('payments', 'public');
        }

        $payment->update($data);

        // Update order status based on payment status
        if ($payment->status === 'confirmed') {
            $payment->order->update(['status' => 'paid']);
        }

        if ($payment->status === 'rejected') {
            $payment->order->update(['status' => 'cancelled']);
        }

        return redirect()->route('payments.index')
            ->with('success', 'Payment status updated successfully');
    }
    
    /**
     * Admin/Organizer verify payment
     */
    public function verify(Request $request, Payment $payment)
    {
        // 🔒 Authorize: Cek apakah organizer bisa verifikasi payment ini
        $this->authorizePayment($payment);
        
        $request->validate([
            'status' => 'required|in:confirmed,rejected',
            'notes' => 'nullable|string'
        ]);
        
        $payment->status = $request->status;
        $payment->verified_by = Auth::id();
        $payment->verified_at = now();
        $payment->notes = $request->notes;
        $payment->save();
        
        // Update order status
        if ($request->status === 'confirmed') {
            $payment->order->update(['status' => 'paid']);
        } else {
            $payment->order->update(['status' => 'cancelled']);
        }
        
        $statusText = $request->status === 'confirmed' ? 'confirmed' : 'rejected';
        
        return redirect()->route('payments.show', $payment)
            ->with('success', "Payment has been {$statusText}");
    }

    public function destroy(Payment $payment)
    {
        // 🔒 Authorize: Hanya admin yang bisa hapus payment
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only admin can delete payment');
        }
        
        if ($payment->payment_proof) {
            Storage::disk('public')->delete($payment->payment_proof);
        }

        $payment->delete();

        return redirect()->route('payments.index')
            ->with('success', 'Payment deleted successfully');
    }
    
    /**
     * 🔒 Authorization helper untuk Payment
     */
    private function authorizePayment(Payment $payment)
    {
        $user = Auth::user();
        
        // Admin bisa akses semua
        if ($user->role === 'admin') {
            return;
        }
        
        // Organizer hanya bisa akses payment dari event miliknya
        if ($user->role === 'organizer') {
            $event = Event::find($payment->order->event_id);
            if (!$event || $event->created_by !== $user->id) {
                abort(403, 'Tidak diizinkan mengakses payment ini');
            }
            return;
        }
        
        // Participant hanya bisa akses payment miliknya sendiri
        if ($user->role === 'participant') {
            if ($payment->order->user_id !== $user->id) {
                abort(403, 'Tidak diizinkan mengakses payment ini');
            }
            return;
        }
        
        abort(403, 'Unauthorized');
    }
}