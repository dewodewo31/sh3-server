<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use Illuminate\Http\Request;

class EventApiController extends Controller
{
    /**
     * Get all events
     */
    public function index(Request $request)
    {
        $query = Event::with(['category', 'creator']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'upcoming') {
                $query->where('start_date', '>', now());
            } elseif ($request->status == 'ongoing') {
                $query->where('start_date', '<=', now())->where('end_date', '>=', now());
            } elseif ($request->status == 'finished') {
                $query->where('end_date', '<', now());
            }
        }

        // Filter by category
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        // Search by title
        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $events = $query->latest()->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $this->formatEvents($events->items()),
            'meta' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
            ]
        ]);
    }

    /**
     * Get single event
     */
    public function show($id)
    {
        $event = Event::with(['category', 'creator', 'galleries'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->formatEvent($event)
        ]);
    }

    /**
     * Get event by slug
     */
    public function showBySlug($slug)
    {
        $event = Event::with(['category', 'creator', 'galleries'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $this->formatEvent($event)
        ]);
    }

    /**
     * Book an event (create order)
     */
    public function book(Request $request, $id)
    {
        $participant = $request->user();
        $event = Event::findOrFail($id);

        // Check if already booked
        $existingOrder = Order::where('participant_id', $participant->id)
            ->where('event_id', $event->id)
            ->exists();

        if ($existingOrder) {
            return response()->json([
                'success' => false,
                'message' => 'You have already booked this event'
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
            'message' => 'Event booked successfully',
            'data' => [
                'order_id' => $order->id,
                'invoice_number' => $order->invoice_number,
                'ticket_code' => $order->ticket_code,
                'status' => $order->status
            ]
        ], 201);
    }

    /**
     * Get my booked events
     */
    public function myEvents(Request $request)
    {
        $participant = $request->user();
        
        $orders = Order::with(['event'])
            ->where('participant_id', $participant->id)
            ->latest()
            ->paginate(15);

        $events = $orders->map(function($order) {
            return $this->formatEvent($order->event, $order);
        });

        return response()->json([
            'success' => true,
            'data' => $events,
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ]
        ]);
    }

    private function formatEvents($events)
    {
        return collect($events)->map(function($event) {
            return $this->formatEvent($event);
        });
    }

    private function formatEvent($event, $order = null)
    {
        $registeredCount = $event->orders()->count();
        
        return [
            'id' => $event->id,
            'title' => $event->title,
            'slug' => $event->slug,
            'description' => $event->description,
            'location' => $event->location,
            'latitude' => $event->latitude,
            'longitude' => $event->longitude,
            'image_url' => $event->image ? asset('storage/' . $event->image) : null,
            'start_date' => $event->start_date->toISOString(),
            'end_date' => $event->end_date->toISOString(),
            'price' => $event->price,
            'price_formatted' => $event->price > 0 ? 'Rp ' . number_format($event->price, 0, ',', '.') : 'GRATIS',
            'quota' => $event->quota,
            'registered_count' => $registeredCount,
            'remaining_quota' => $event->quota - $registeredCount,
            'status' => $event->status,
            'category' => [
                'id' => $event->category->id,
                'name' => $event->category->name,
            ],
            'key_points' => $event->key_point,
            'galleries' => $this->formatGalleries($event->galleries),
            'order' => $order ? [
                'order_id' => $order->id,
                'invoice_number' => $order->invoice_number,
                'ticket_code' => $order->ticket_code,
                'status' => $order->status
            ] : null,
        ];
    }

    private function formatGalleries($galleries)
    {
        $images = [];
        foreach ($galleries as $gallery) {
            if ($gallery->image && is_array($gallery->image)) {
                foreach ($gallery->image as $image) {
                    $images[] = asset('storage/' . $image);
                }
            }
        }
        return $images;
    }
     public function participants($id, Request $request)
    {
        $event = Event::findOrFail($id);
        $currentParticipant = $request->user();

        // Check if current user has joined this event
        $hasJoined = Order::where('participant_id', $currentParticipant->id)
            ->where('event_id', $event->id)
            ->whereIn('status', ['paid', 'free'])
            ->exists();

        if (!$hasJoined) {
            return response()->json([
                'success' => false,
                'message' => 'You must join this event to see other participants'
            ], 403);
        }

        // Get all participants who joined this event (paid or free)
        $participants = Order::with(['participant'])
            ->where('event_id', $event->id)
            ->whereIn('status', ['paid', 'free'])
            ->get()
            ->map(function($order) {
                return [
                    'id' => $order->participant->id,
                    'hash_id' => $order->participant->hash_id,
                    'name' => $order->participant->name,
                    'email' => $order->participant->email,
                    'phone' => $order->participant->phone,
                    'gender' => $order->participant->gender,
                    'photo_url' => $order->participant->photo ? asset('storage/' . $order->participant->photo) : null,
                    'joined_at' => $order->created_at->toISOString(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'event' => [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start_date' => $event->start_date->toISOString(),
                ],
                'total_participants' => $participants->count(),
                'participants' => $participants
            ]
        ]);
    }

    /**
     * Get participant count for an event (public)
     * GET /api/v1/events/{id}/participants/count
     */
    public function participantCount($id)
    {
        $event = Event::findOrFail($id);
        
        $count = Order::where('event_id', $event->id)
            ->whereIn('status', ['paid', 'free'])
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'event_id' => $event->id,
                'event_title' => $event->title,
                'participants_count' => $count
            ]
        ]);
    }
}