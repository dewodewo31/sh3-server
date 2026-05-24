<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Services\PaymentService\PaymentServiceInterface;
use Illuminate\Http\Request;

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
        if (!$this->paymentService->authorizePayment($id)) {
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
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only admin can update payment');
        }
        
        $this->paymentService->updatePayment($id, $request->validated());

        return redirect()->route('payments.index')
            ->with('success', 'Payment status updated successfully');
    }
    
    /**
     * Verify payment (Admin/Organizer)
     */
    public function verify(Request $request, $id)
    {
        if (!$this->paymentService->authorizePayment($id)) {
            abort(403, 'Tidak diizinkan');
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
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Only admin can delete payment');
        }
        
        $this->paymentService->deletePayment($id);

        return redirect()->route('payments.index')
            ->with('success', 'Payment deleted successfully');
    }
}