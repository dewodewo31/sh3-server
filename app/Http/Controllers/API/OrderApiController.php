<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Event;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderApiController extends Controller
{
    /**
     * Create new order
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'participant_id' => 'required|exists:participants,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $event = Event::find($request->event_id);
        $participant = Participant::find($request->participant_id);

        // Check if already ordered
        $existingOrder = Order::where('participant_id', $participant->id)
            ->where('event_id', $event->id)
            ->exists();

        if ($existingOrder) {
            return response()->json([
                'success' => false,
                'message' => 'You have already registered for this event'
            ], 400);
        }

        // Check quota
        $registeredCount = $event->orders()->count();
        if ($registeredCount >= $event->quota) {
            return response()->json([
                'success' => false,
                'message' => 'Event quota is full'
            ], 400);
        }

        $order = Order::create([
            'participant_id' => $participant->id,
            'event_id' => $event->id,
            'total_price' => $event->price,
            'status' => $event->price > 0 ? 'pending' : 'free'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order created successfully',
            'data' => new OrderResource($order->load(['participant', 'event']))
        ], 201);
    }

    /**
     * Get my orders (authenticated participant)
     */
    public function myOrders(Request $request)
    {
        $participant = $request->user();
        
        $orders = Order::with(['event', 'payment'])
            ->where('participant_id', $participant->id)
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => OrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ]
        ]);
    }

    /**
     * Get order detail
     */
    public function show($id)
    {
        $order = Order::with(['participant', 'event', 'payment'])->findOrFail($id);

        // Check authorization
        $participant = request()->user();
        if ($order->participant_id !== $participant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => new OrderResource($order)
        ]);
    }

    /**
     * Upload payment proof
     */
    public function uploadPaymentProof(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $participant = $request->user();

        if ($order->participant_id !== $participant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if (in_array($order->status, ['paid', 'cancelled', 'free'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be updated'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'paid_at' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->amount < $order->total_price) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount is less than total price'
            ], 400);
        }

        // Upload proof
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

        return response()->json([
            'success' => true,
            'message' => 'Payment proof uploaded. Waiting for verification.'
        ]);
    }

    /**
     * Cancel order
     */
    public function cancel($id)
    {
        $order = Order::findOrFail($id);
        $participant = request()->user();

        if ($order->participant_id !== $participant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled'
            ], 400);
        }

        $order->status = 'cancelled';
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully'
        ]);
    }
    /**
     * Get my tickets (valid tickets only - paid or free)
     */
    public function myTickets(Request $request)
    {
        $participant = $request->user();
        
        $orders = Order::with(['event'])
            ->where('participant_id', $participant->id)
            ->whereIn('status', ['paid', 'free'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders->map(function($order) {
                return [
                    'ticket_code' => $order->ticket_code,
                    'invoice_number' => $order->invoice_number,
                    'event_name' => $order->event->title,
                    'event_date' => $order->event->start_date->format('d M Y H:i'),
                    'event_location' => $order->event->location,
                    'status' => $order->status,
                ];
            })
        ]);
    }

    /**
     * Get ticket detail
     */
    public function getTicketDetail($ticket_code, Request $request)
    {
        $order = Order::with(['event', 'participant', 'payment'])
            ->where('ticket_code', $ticket_code)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found'
            ], 404);
        }

        $participant = $request->user();
        if ($order->participant_id !== $participant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'ticket_code' => $order->ticket_code,
                'invoice_number' => $order->invoice_number,
                'event' => [
                    'id' => $order->event->id,
                    'title' => $order->event->title,
                    'description' => $order->event->description,
                    'location' => $order->event->location,
                    'start_date' => $order->event->start_date->toISOString(),
                    'end_date' => $order->event->end_date->toISOString(),
                ],
                'participant' => [
                    'name' => $order->participant->name,
                    'email' => $order->participant->email,
                ],
                'status' => $order->status,
                'created_at' => $order->created_at->toISOString(),
            ]
        ]);
    }
    /**
     * Check ticket (public - no auth required)
     * GET /api/v1/check-ticket/{ticket_code}
     */
    public function checkTicket($ticket_code)
    {
        $order = Order::with(['event', 'participant'])
            ->where('ticket_code', $ticket_code)
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
                'event_date' => $order->event->start_date->format('d M Y H:i'),
                'event_location' => $order->event->location,
                'participant_name' => $order->participant->name,
                'status' => $order->status,
                'is_valid' => in_array($order->status, ['paid', 'free'])
            ]
        ]);
    }
}