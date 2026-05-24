<?php

namespace App\Services\PaymentService;

use App\Models\Payment;
use App\Models\Event;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentService implements PaymentServiceInterface
{
    protected $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Get payments with filters and pagination
     */
    public function getPayments(Request $request): LengthAwarePaginator
    {
        $user = Auth::user();
        $query = Payment::with(['order', 'order.participant', 'order.event']);

        // ORGANIZER: Only see payments from their events
        if ($user->role === 'organizer') {
            $eventIds = Event::where('created_by', $user->id)->pluck('id');
            $query->whereHas('order', function($q) use ($eventIds) {
                $q->whereIn('event_id', $eventIds);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Search by invoice or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('order', function($q2) use ($search) {
                    $q2->where('invoice_number', 'like', "%{$search}%")
                       ->orWhereHas('participant', function($q3) use ($search) {
                           $q3->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                       });
                });
            });
        }

        return $query->latest()->paginate($request->per_page ?? 15);
    }

    /**
     * Get payment statistics
     */
    public function getPaymentStats(Request $request): array
    {
        $user = Auth::user();
        
        if ($user->role === 'organizer') {
            $eventIds = Event::where('created_by', $user->id)->pluck('id');
            $query = Payment::whereHas('order', function($q) use ($eventIds) {
                $q->whereIn('event_id', $eventIds);
            });
        } else {
            $query = Payment::query();
        }

        // Apply status filter to stats
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return [
            'totalPayments' => (clone $query)->count(),
            'pendingPayments' => (clone $query)->where('status', 'pending')->count(),
            'confirmedPayments' => (clone $query)->where('status', 'confirmed')->count(),
            'rejectedPayments' => (clone $query)->where('status', 'rejected')->count(),
            'totalAmount' => (clone $query)->where('status', 'confirmed')->sum('amount'),
        ];
    }

    /**
     * Get filter data (payment methods, statuses)
     */
    public function getFilterData(): array
    {
        $paymentMethods = [
            'Bank Transfer BCA',
            'Bank Transfer Mandiri',
            'Bank Transfer BRI',
            'QRIS',
            'DANA',
            'OVO',
            'GoPay'
        ];
        
        $statuses = ['pending', 'confirmed', 'rejected'];
        
        return compact('paymentMethods', 'statuses');
    }

    /**
     * Get payment by ID
     */
    public function getPaymentById($id)
    {
        return Payment::with(['order.participant', 'order.event', 'verifier'])->findOrFail($id);
    }

    /**
     * Create new payment
     */
    public function createPayment(array $data)
    {
        // Handle payment proof upload
        if (isset($data['payment_proof']) && $data['payment_proof']) {
            $data['payment_proof'] = $data['payment_proof']->store('payments', 'public');
        }
        
        $data['status'] = 'pending';
        $data['paid_at'] = now();

        $payment = Payment::create($data);
        
        // Update order status
        if ($payment->order) {
            $payment->order->update(['status' => 'pending']);
        }
        
        return $payment;
    }

    /**
     * Update payment (Admin only)
     */
    public function updatePayment($id, array $data)
    {
        $payment = Payment::findOrFail($id);
        
        // Handle payment proof upload
        if (isset($data['payment_proof']) && $data['payment_proof']) {
            if ($payment->payment_proof) {
                Storage::disk('public')->delete($payment->payment_proof);
            }
            $data['payment_proof'] = $data['payment_proof']->store('payments', 'public');
        }

        $payment->update($data);

        // Update order status based on payment status
        if ($payment->status === 'confirmed') {
            $payment->order->update(['status' => 'paid']);
        }

        if ($payment->status === 'rejected') {
            $payment->order->update(['status' => 'cancelled']);
        }
        
        return $payment;
    }

    /**
     * Verify payment (Admin/Organizer)
     */
    public function verifyPayment($id, array $data)
    {
        $payment = Payment::findOrFail($id);
        
        $payment->status = $data['status'];
        $payment->verified_by = Auth::id();
        $payment->verified_at = now();
        $payment->notes = $data['notes'] ?? null;
        $payment->save();
        
        // Update order status
        if ($data['status'] === 'confirmed') {
            $payment->order->update(['status' => 'paid']);
        } else {
            $payment->order->update(['status' => 'cancelled']);
        }
        
        return $payment;
    }

    /**
     * Delete payment (Admin only)
     */
    public function deletePayment($id)
    {
        $payment = Payment::findOrFail($id);
        
        if ($payment->payment_proof) {
            Storage::disk('public')->delete($payment->payment_proof);
        }

        return $payment->delete();
    }

    /**
     * Authorize payment access
     */
    public function authorizePayment($id): bool
    {
        $user = Auth::user();
        $payment = Payment::findOrFail($id);
        
        // Admin can access all
        if ($user->role === 'admin') {
            return true;
        }
        
        // Organizer can only access payments from their events
        if ($user->role === 'organizer') {
            $event = Event::find($payment->order->event_id);
            return $event && $event->created_by === $user->id;
        }
        
        // Participant can only access their own payments
        if ($user->role === 'participant') {
            return $payment->order->participant_id === $user->id;
        }
        
        return false;
    }
}