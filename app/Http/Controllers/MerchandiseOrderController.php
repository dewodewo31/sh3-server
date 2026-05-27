<?php

namespace App\Http\Controllers;

use App\Models\Merchandise;
use App\Models\MerchandiseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MerchandiseOrderController extends Controller
{
    /**
     * Display all merchandise orders (Admin)
     */
    public function index(Request $request)
    {
        $query = MerchandiseOrder::with(['participant', 'merchandise']);
        
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('search') && $request->search != '') {
            $query->where('invoice_number', 'like', '%' . $request->search . '%')
                ->orWhereHas('participant', function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });
        }
        
        $orders = $query->latest()->paginate(15);
        
        $stats = [
            'total' => MerchandiseOrder::count(),
            'pending' => MerchandiseOrder::where('status', 'pending')->count(),
            'processing' => MerchandiseOrder::where('status', 'processing')->count(),
            'shipped' => MerchandiseOrder::where('status', 'shipped')->count(),
            'delivered' => MerchandiseOrder::where('status', 'delivered')->count(),
            'cancelled' => MerchandiseOrder::where('status', 'cancelled')->count(),
            'total_revenue' => MerchandiseOrder::where('status', 'delivered')->sum('total_price'),
        ];
        
        $statuses = ['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled'];
        
        return view('merchandise.orders.index', compact('orders', 'stats', 'statuses'));
    }
    
    /**
     * Update order status
     */
    public function updateStatus(Request $request, MerchandiseOrder $order)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,processing,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string',
            'shipping_courier' => 'nullable|string'
        ]);
        
        $order->status = $request->status;
        
        if ($request->status == 'shipped') {
            $order->shipped_at = now();
        }
        
        if ($request->status == 'delivered') {
            $order->delivered_at = now();
        }
        
        if ($request->filled('tracking_number')) {
            $order->tracking_number = $request->tracking_number;
        }
        
        if ($request->filled('shipping_courier')) {
            $order->shipping_courier = $request->shipping_courier;
        }
        
        $order->save();
        
        return redirect()->back()
            ->with('success', 'Order status updated');
    }
    
    /**
     * Show order detail
     */
    public function show(MerchandiseOrder $order)
    {
        $order->load(['participant', 'merchandise']);
        
        return view('merchandise.orders.show', compact('order'));
    }
}