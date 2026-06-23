<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService\OrderServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        $orders = $this->orderService->getOrders($request);
        $stats = $this->orderService->getOrderStats($request);
        $filterData = $this->orderService->getFilterData();
        
        return view('orders.index', array_merge(
            compact('orders'),
            $stats,
            $filterData
        ));
    }

    /**
     * Display order details
     */
    public function show($id)
    {
        if (!$this->authorizeOrder($id)) {
            abort(403, 'Tidak diizinkan');
        }
        
        $order = $this->orderService->getOrderById($id);
        
        return view('orders.show', compact('order'));
    }

    /**
     * Verify payment (Admin/Organizer/Bendahara)
     */
    public function verifyPayment(Request $request, $id)
    {
        if (!$this->canVerifyPayment()) {
            abort(403, 'Tidak diizinkan - Hanya admin, organizer, atau bendahara yang bisa verifikasi');
        }
        
        $request->validate([
            'status' => 'required|in:paid,cancelled',
            'notes' => 'nullable|string'
        ]);
        
        $this->orderService->verifyPayment($id, $request->only(['status', 'notes']));
        
        $statusText = $request->status == 'paid' ? 'diverifikasi' : 'dibatalkan';
        
        return redirect()->route('orders.show', $id)
            ->with('success', "Pembayaran berhasil {$statusText}");
    }
    
    /**
     * Update payment proof (Customer via API)
     */
    public function updatePaymentProof(Request $request, $id)
    {
        try {
            $this->orderService->updatePaymentProof($id, $request->all());
            
            return redirect()->route('orders.show', $id)
                ->with('success', 'Bukti pembayaran berhasil diupload, menunggu verifikasi admin');
        } catch (\Exception $e) {
            return redirect()->route('orders.show', $id)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Delete order (Admin only)
     */
    public function destroy($id)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Only admin can delete orders');
        }
        
        $this->orderService->deleteOrder($id);

        return redirect()->route('orders.index')
            ->with('success', 'Order berhasil dihapus');
    }
    
    /**
     * Cancel order (Admin/Organizer/Bendahara)
     */
    public function cancel($id)
    {
        if (!$this->canCancelOrder()) {
            abort(403, 'Tidak diizinkan - Hanya admin, organizer, atau bendahara yang bisa membatalkan order');
        }
        
        if (!$this->authorizeOrder($id)) {
            abort(403, 'Tidak diizinkan');
        }
        
        try {
            $this->orderService->cancelOrder($id);
            
            return redirect()->route('orders.show', $id)
                ->with('success', 'Order berhasil dibatalkan');
        } catch (\Exception $e) {
            return redirect()->route('orders.show', $id)
                ->with('error', $e->getMessage());
        }
    }
    
    /**
     * Get order by ticket code (public API)
     */
    public function checkTicket(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required|string'
        ]);
        
        $result = $this->orderService->checkTicket($request->ticket_code);
        
        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $result
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
        $result = $this->orderService->exportOrdersToCsv($request);
        
        return response($result['content'])
            ->withHeaders($result['headers']);
    }
    
    /**
     * Export single order to PDF (Invoice)
     */
    public function exportInvoicePdf($id)
    {
        if (!$this->authorizeOrder($id)) {
            abort(403, 'Tidak diizinkan');
        }
        
        return $this->orderService->exportSingleOrderToPdf($id);
    }

    /**
     * Export all orders to PDF
     */
    public function exportAllPdf(Request $request)
    {
        return $this->orderService->exportOrdersToPdf($request);
    }

    // ==================== AUTHORIZATION HELPERS ====================

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
     * Check if user can cancel order
     */
    private function canCancelOrder(): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        
        $allowedRoles = ['admin', 'admin_full_access', 'admin_laman', 'organizer', 'bendahara'];
        return in_array($user->role, $allowedRoles);
    }

    /**
     * Authorize order access
     */
    private function authorizeOrder($id): bool
    {
        $user = Auth::user();
        if (!$user) return false;
        
        // Admin roles have full access
        if ($this->isAdmin()) return true;
        
        // Organizer can only see orders for their events
        if ($user->role === 'organizer') {
            $order = $this->orderService->getOrderById($id);
            if ($order && $order->event) {
                return $order->event->created_by === $user->id;
            }
            return false;
        }
        
        // Bendahara can see all orders
        if ($user->role === 'bendahara') {
            return true;
        }
        
        // Participant can only see their own orders
        if ($user->role === 'participant') {
            $order = $this->orderService->getOrderById($id);
            if ($order) {
                return $order->participant_id === $user->id;
            }
            return false;
        }
        
        return false;
    }
}