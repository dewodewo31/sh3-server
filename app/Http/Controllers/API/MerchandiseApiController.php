<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Merchandise;
use App\Models\MerchandiseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MerchandiseApiController extends Controller
{
    /**
     * Get all merchandise (public)
     * GET /api/v1/merchandise
     */
    public function index(Request $request)
    {
        $query = Merchandise::active();
        
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }
        
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('sort') && $request->sort == 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($request->has('sort') && $request->sort == 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->latest();
        }
        
        $merchandise = $query->paginate($request->per_page ?? 15);
        
        return response()->json([
            'success' => true,
            'data' => $merchandise->items(),
            'meta' => [
                'current_page' => $merchandise->currentPage(),
                'last_page' => $merchandise->lastPage(),
                'per_page' => $merchandise->perPage(),
                'total' => $merchandise->total(),
            ]
        ]);
    }
    
    /**
     * Get merchandise detail
     * GET /api/v1/merchandise/{id}
     */
    public function show($id)
    {
        $merchandise = Merchandise::active()->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $merchandise
        ]);
    }
    
    /**
     * Get merchandise categories
     * GET /api/v1/merchandise/categories
     */
    public function categories()
    {
        $categories = [
            ['id' => 'clothing', 'name' => 'Clothing', 'icon' => '👕'],
            ['id' => 'accessories', 'name' => 'Accessories', 'icon' => '🧢'],
            ['id' => 'collectibles', 'name' => 'Collectibles', 'icon' => '🎁'],
            ['id' => 'others', 'name' => 'Others', 'icon' => '📦'],
        ];
        
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
    
    /**
     * Create merchandise order (participant buy)
     * POST /api/v1/merchandise/order
     */
    public function createOrder(Request $request)
    {
        $participant = $request->user();
        
        $validator = Validator::make($request->all(), [
            'merchandise_id' => 'required|exists:merchandise,id',
            'quantity' => 'required|integer|min:1',
            'size' => 'nullable|string',
            'color' => 'nullable|string',
            'shipping_address' => 'required|string',
            'shipping_phone' => 'required|string',
            'notes' => 'nullable|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $merchandise = Merchandise::findOrFail($request->merchandise_id);
        
        // Check stock
        if ($merchandise->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Available: ' . $merchandise->stock
            ], 400);
        }
        
        // Calculate total price
        $totalPrice = $merchandise->price * $request->quantity;
        
        // Create order
        $order = MerchandiseOrder::create([
            'participant_id' => $participant->id,
            'merchandise_id' => $merchandise->id,
            'quantity' => $request->quantity,
            'size' => $request->size,
            'color' => $request->color,
            'unit_price' => $merchandise->price,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'shipping_address' => $request->shipping_address,
            'shipping_phone' => $request->shipping_phone,
            'notes' => $request->notes
        ]);
        
        // Reduce stock
        $merchandise->reduceStock($request->quantity);
        
        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => [
                'order_id' => $order->id,
                'invoice_number' => $order->invoice_number,
                'total_price' => $order->total_price,
                'status' => $order->status,
                'payment_instructions' => 'Please transfer to BCA 1234567890 a.n SH3 Event'
            ]
        ], 201);
    }
    
    /**
     * Get my merchandise orders
     * GET /api/v1/merchandise/my-orders
     */
    public function myOrders(Request $request)
    {
        $participant = $request->user();
        
        $orders = MerchandiseOrder::with('merchandise')
            ->where('participant_id', $participant->id)
            ->latest()
            ->paginate(15);
        
        return response()->json([
            'success' => true,
            'data' => $orders->items(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ]
        ]);
    }
    
    /**
     * Get merchandise order detail
     * GET /api/v1/merchandise/orders/{id}
     */
    public function orderDetail($id, Request $request)
    {
        $participant = $request->user();
        
        $order = MerchandiseOrder::with('merchandise')
            ->where('participant_id', $participant->id)
            ->where('id', $id)
            ->first();
        
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }
    
    /**
     * Cancel merchandise order
     * POST /api/v1/merchandise/orders/{id}/cancel
     */
    public function cancelOrder($id, Request $request)
    {
        $participant = $request->user();
        
        $order = MerchandiseOrder::where('participant_id', $participant->id)
            ->where('id', $id)
            ->first();
        
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
        
        if (!in_array($order->status, ['pending', 'paid'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled'
            ], 400);
        }
        
        // Restore stock
        $merchandise = $order->merchandise;
        $merchandise->stock += $order->quantity;
        $merchandise->save();
        
        $order->status = 'cancelled';
        $order->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully'
        ]);
    }
}