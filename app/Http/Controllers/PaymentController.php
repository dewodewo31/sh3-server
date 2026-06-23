<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Services\PaymentService\PaymentServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentServiceInterface $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of payments
     */
    public function index(Request $request)
    {
        $payments = $this->paymentService->getPayments($request);
        $stats = $this->paymentService->getPaymentStats($request);
        $filterData = $this->paymentService->getFilterData();
        
        return view('payments.index', array_merge(
            compact('payments'),
            $stats,
            $filterData
        ));
    }

    /**
     * Display payment details
     */
    public function show($id)
    {
        if (!$this->authorizePayment($id)) {
            abort(403, 'Tidak diizinkan');
        }
        
        $payment = $this->paymentService->getPaymentById($id);
        
        return view('payments.show', [
            'payment' => $payment,
            'order' => $payment->order
        ]);
    }

    /**
     * Store new payment
     */
    public function store(StorePaymentRequest $request)
    {
        $payment = $this->paymentService->createPayment($request->validated());

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Payment proof has been uploaded. Waiting for admin verification.');
    }

    /**
     * Update payment (Admin only)
     */
    public function update(UpdatePaymentRequest $request, $id)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Only admin can update payment');
        }
        
        $this->paymentService->updatePayment($id, $request->validated());

        return redirect()->route('payments.index')
            ->with('success', 'Payment status updated successfully');
    }
    
    /**
     * Verify payment (Admin/Organizer/Bendahara)
     */
    public function verify(Request $request, $id)
    {
        if (!$this->canVerifyPayment()) {
            abort(403, 'Tidak diizinkan - Hanya admin, organizer, atau bendahara yang bisa verifikasi');
        }
        
        $request->validate([
            'status' => 'required|in:confirmed,rejected',
            'notes' => 'nullable|string'
        ]);
        
        $this->paymentService->verifyPayment($id, $request->only(['status', 'notes']));
        
        $statusText = $request->status === 'confirmed' ? 'confirmed' : 'rejected';
        
        return redirect()->route('payments.show', $id)
            ->with('success', "Payment has been {$statusText}");
    }

    /**
     * Delete payment (Admin only)
     */
    public function destroy($id)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Only admin can delete payment');
        }
        
        $this->paymentService->deletePayment($id);

        return redirect()->route('payments.index')
            ->with('success', 'Payment deleted successfully');
    }

    /**
     * Check if user is admin (including all admin roles)
     */
    private function isAdmin(): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        
        $adminRoles = ['admin', 'admin_full_access', 'admin_laman', 'admin_member', 'admin_bnh'];
        return in_array($user->role, $adminRoles);
    }

    /**
     * Check if user can verify payment
     */
    private function canVerifyPayment(): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        
        $allowedRoles = ['admin', 'admin_full_access', 'admin_laman', 'organizer', 'bendahara'];
        return in_array($user->role, $allowedRoles);
    }

    /**
     * Authorize payment access
     */
    private function authorizePayment($id): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        
        // Admin roles have full access
        if ($this->isAdmin()) return true;
        
        // Organizer can only see payments for their events
        if ($user->role === 'organizer') {
            $payment = $this->paymentService->getPaymentById($id);
            if ($payment && $payment->order && $payment->order->event) {
                return $payment->order->event->created_by === $user->id;
            }
            return false;
        }
        
        // Bendahara can see all payments
        if ($user->role === 'bendahara') {
            return true;
        }
        
        return false;
    }
}