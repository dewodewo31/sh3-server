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
        
        // Format data dengan full URL untuk image
        $formattedData = collect($merchandise->items())->map(function($item) {
            return $this->formatMerchandise($item);
        });
        
        return response()->json([
            'success' => true,
            'data' => $formattedData,
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
            'data' => $this->formatMerchandise($merchandise)
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
        
        // Format response dengan merchandise detail
        $orderData = [
            'order_id' => $order->id,
            'invoice_number' => $order->invoice_number,
            'merchandise' => $this->formatMerchandise($merchandise),
            'quantity' => $order->quantity,
            'size' => $order->size,
            'color' => $order->color,
            'unit_price' => $order->unit_price,
            'total_price' => $order->total_price,
            'status' => $order->status,
            'shipping_address' => $order->shipping_address,
            'shipping_phone' => $order->shipping_phone,
            'created_at' => $order->created_at,
            'payment_instructions' => 'Please transfer to BCA 1234567890 a.n SH3 Event'
        ];
        
        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => $orderData
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
        
        // Format data dengan full URL untuk image merchandise
        $formattedOrders = $orders->items();
        foreach ($formattedOrders as $order) {
            if ($order->merchandise) {
                $order->merchandise->image_url = $order->merchandise->image 
                    ? asset('storage/' . $order->merchandise->image) 
                    : null;
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $formattedOrders,
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
        
        // Format merchandise image URL
        if ($order->merchandise) {
            $order->merchandise->image_url = $order->merchandise->image 
                ? asset('storage/' . $order->merchandise->image) 
                : null;
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
    
    // ==================== PRIVATE METHODS ====================
    
    /**
     * Format merchandise data dengan full URL untuk image
     */
    private function formatMerchandise($merchandise)
    {
        return [
            'id' => $merchandise->id,
            'name' => $merchandise->name,
            'slug' => $merchandise->slug,
            'description' => $merchandise->description,
            'image_url' => $merchandise->image ? asset('storage/' . $merchandise->image) : null,
            'price' => $merchandise->price,
            'price_formatted' => 'Rp ' . number_format($merchandise->price, 0, ',', '.'),
            'stock' => $merchandise->stock,
            'category' => $merchandise->category,
            'sizes' => $merchandise->sizes ?? [],
            'colors' => $merchandise->colors ?? [],
            'is_active' => $merchandise->is_active,
            'sold_count' => $merchandise->sold_count,
            'created_at' => $merchandise->created_at,
            'updated_at' => $merchandise->updated_at,
        ];
    }
    /**
     * Upload payment proof for merchandise order
     * POST /api/v1/merchandise/orders/{id}/upload-payment
     */
    public function uploadPaymentProof($id, Request $request)
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
        
        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Payment cannot be uploaded for this order status'
            ], 400);
        }
        
        $validator = Validator::make($request->all(), [
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,pdf|max:2048',
            'payment_method' => 'required|string|in:bank_transfer,qris,ewallet',
            'paid_amount' => 'required|numeric|min:0',
            'bank_name' => 'nullable|string', // Optional: nama bank pengirim
            'account_name' => 'nullable|string', // Optional: nama pemilik rekening
            'account_number' => 'nullable|string', // Optional: nomor rekening
            'notes' => 'nullable|string' // Optional: catatan tambahan
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Upload payment proof
        if ($request->hasFile('payment_proof')) {
            // Delete old payment proof if exists
            if ($order->payment_proof) {
                Storage::disk('public')->delete($order->payment_proof);
            }
            
            $path = $request->file('payment_proof')->store('merchandise-payments', 'public');
            $order->payment_proof = $path;
        }
        
        // Update order with payment info
        $order->payment_proof_uploaded_at = now();
        $order->paid_amount = $request->paid_amount;
        $order->payment_method = $request->payment_method;
        
        // Save additional payment info as JSON in notes or separate column
        $paymentDetails = [
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'participant_notes' => $request->notes
        ];
        
        // Merge with existing notes
        $existingNotes = json_decode($order->notes ?? '{}', true);
        $existingNotes['payment_details'] = $paymentDetails;
        $order->notes = json_encode($existingNotes);
        
        $order->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Payment proof uploaded successfully. Waiting for verification.',
            'data' => [
                'order_id' => $order->id,
                'status' => $order->status,
                'payment_proof_url' => $order->payment_proof_url,
                'uploaded_at' => $order->payment_proof_uploaded_at,
                'payment_method' => $order->payment_method,
                'paid_amount' => $order->paid_amount,
                'verification_status' => 'pending'
            ]
        ]);
    }
    
    /**
     * Get payment status for order
     * GET /api/v1/merchandise/orders/{id}/payment-status
     */
    public function getPaymentStatus($id, Request $request)
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
        
        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => $order->id,
                'invoice_number' => $order->invoice_number,
                'status' => $order->status,
                'total_price' => $order->total_price,
                'paid_amount' => $order->paid_amount,
                'remaining_amount' => $order->total_price - ($order->paid_amount ?? 0),
                'payment_proof_uploaded' => !is_null($order->payment_proof),
                'payment_proof_url' => $order->payment_proof_url,
                'payment_method' => $order->payment_method,
                'uploaded_at' => $order->payment_proof_uploaded_at,
                'verified_at' => $order->verified_at,
                'bank_account_info' => [
                    'bank_name' => 'BCA',
                    'account_number' => '1234567890',
                    'account_name' => 'SH3 Event Organizer',
                ]
            ]
        ]);
    }
}