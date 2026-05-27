<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use App\Models\Sponsor;
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
     * Get single event detail with sponsors and merchandise
     * (HANYA SATU method show - hapus yang lain)
     */
    public function show($id)
    {
        $event = Event::with(['category', 'creator', 'galleries', 'sponsors', 'merchandise'])
            ->findOrFail($id);
        
        // Format sponsors by tier
        $sponsors = [
            'platinum' => $event->sponsors->where('tier', 'platinum')->values(),
            'gold' => $event->sponsors->where('tier', 'gold')->values(),
            'silver' => $event->sponsors->where('tier', 'silver')->values(),
            'bronze' => $event->sponsors->where('tier', 'bronze')->values(),
            'partner' => $event->sponsors->where('tier', 'partner')->values(),
        ];

        // Format merchandise for this event
        $merchandise = $this->formatEventMerchandise($event);

        return response()->json([
            'success' => true,
            'data' => [
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
                'registered_count' => $event->orders()->count(),
                'remaining_quota' => $event->quota - $event->orders()->count(),
                'status' => $event->status,
                'category' => [
                    'id' => $event->category->id,
                    'name' => $event->category->name,
                ],
                'creator' => [
                    'id' => $event->creator->id,
                    'name' => $event->creator->name,
                ],
                'key_points' => $event->key_point,
                'galleries' => $this->formatGalleries($event->galleries),
                'sponsors' => $sponsors,
                'merchandise' => $merchandise,
                'created_at' => $event->created_at,
                'updated_at' => $event->updated_at,
            ]
        ]);
    }

    /**
     * Get event by slug with sponsors and merchandise
     */
    public function showBySlug($slug)
    {
        $event = Event::with(['category', 'creator', 'galleries', 'sponsors', 'merchandise'])
            ->where('slug', $slug)
            ->firstOrFail();
        
        $sponsors = [
            'platinum' => $event->sponsors->where('tier', 'platinum')->values(),
            'gold' => $event->sponsors->where('tier', 'gold')->values(),
            'silver' => $event->sponsors->where('tier', 'silver')->values(),
            'bronze' => $event->sponsors->where('tier', 'bronze')->values(),
            'partner' => $event->sponsors->where('tier', 'partner')->values(),
        ];

        // Format merchandise for this event
        $merchandise = $this->formatEventMerchandise($event);

        return response()->json([
            'success' => true,
            'data' => [
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
                'registered_count' => $event->orders()->count(),
                'remaining_quota' => $event->quota - $event->orders()->count(),
                'status' => $event->status,
                'category' => [
                    'id' => $event->category->id,
                    'name' => $event->category->name,
                ],
                'creator' => [
                    'id' => $event->creator->id,
                    'name' => $event->creator->name,
                ],
                'key_points' => $event->key_point,
                'galleries' => $this->formatGalleries($event->galleries),
                'sponsors' => $sponsors,
                'merchandise' => $merchandise,
                'created_at' => $event->created_at,
                'updated_at' => $event->updated_at,
            ]
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

    /**
     * Get participants for an event
     */
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

    /**
     * Get all sponsors (public)
     */
    public function getSponsors(Request $request)
    {
        $query = Sponsor::with('events')->active();
        
        if ($request->has('tier') && $request->tier != '') {
            $query->where('tier', $request->tier);
        }
        
        $sponsors = $query->orderBy('sort_order')->get();
        
        // Group by tier
        $grouped = [
            'platinum' => $sponsors->where('tier', 'platinum')->values(),
            'gold' => $sponsors->where('tier', 'gold')->values(),
            'silver' => $sponsors->where('tier', 'silver')->values(),
            'bronze' => $sponsors->where('tier', 'bronze')->values(),
            'partner' => $sponsors->where('tier', 'partner')->values(),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $grouped
        ]);
    }

    /**
     * Get merchandise for specific event
     */
    public function eventMerchandise($id)
    {
        $event = Event::findOrFail($id);
        
        $merchandise = $this->formatEventMerchandise($event);

        return response()->json([
            'success' => true,
            'data' => [
                'event_id' => $event->id,
                'event_title' => $event->title,
                'merchandise' => $merchandise
            ]
        ]);
    }

    /**
     * Get a specific merchandise detail for an event
     */
    public function eventMerchandiseDetail($eventId, $merchandiseId)
    {
        $event = Event::findOrFail($eventId);
        
        $merchandise = $event->merchandise()
            ->where('merchandise.id', $merchandiseId)
            ->firstOrFail();

        $formattedMerchandise = $this->formatSingleMerchandise($event, $merchandise);

        return response()->json([
            'success' => true,
            'data' => $formattedMerchandise
        ]);
    }

    // ==================== PRIVATE METHODS ====================

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

    /**
     * Format merchandise for an event
     */
    private function formatEventMerchandise($event)
    {
        if (!$event->merchandise || $event->merchandise->count() == 0) {
            return [];
        }

        $merchandise = $event->merchandise->filter(function($item) {
            return $item->pivot->is_available ?? true;
        });

        return $merchandise->map(function($item) use ($event) {
            return $this->formatSingleMerchandise($event, $item);
        })->values();
    }

    /**
     * Format single merchandise item
     */
    private function formatSingleMerchandise($event, $merchandise)
    {
        $eventPrice = $merchandise->pivot->discount_price ?? $merchandise->price;
        $eventStock = $merchandise->pivot->event_stock ?? $merchandise->stock;
        $hasDiscount = $merchandise->pivot->discount_price !== null && $merchandise->pivot->discount_price < $merchandise->price;

        return [
            'id' => $merchandise->id,
            'name' => $merchandise->name,
            'slug' => $merchandise->slug,
            'description' => $merchandise->description,
            'image_url' => $merchandise->image ? asset('storage/' . $merchandise->image) : null,
            'category' => $merchandise->category,
            'sizes' => $merchandise->sizes ?? [],
            'colors' => $merchandise->colors ?? [],
            
            // Pricing
            'price' => $merchandise->price,
            'price_formatted' => 'Rp ' . number_format($merchandise->price, 0, ',', '.'),
            'event_price' => $eventPrice,
            'event_price_formatted' => 'Rp ' . number_format($eventPrice, 0, ',', '.'),
            'has_discount' => $hasDiscount,
            'discount_amount' => $hasDiscount ? $merchandise->price - $eventPrice : 0,
            'discount_percentage' => $hasDiscount ? round((($merchandise->price - $eventPrice) / $merchandise->price) * 100) : 0,
            
            // Stock
            'stock' => $eventStock,
            'is_in_stock' => $eventStock > 0,
            'stock_status' => $eventStock > 10 ? 'available' : ($eventStock > 0 ? 'limited' : 'sold_out'),
            
            // Pivot data
            'event_specific' => [
                'discount_price' => $merchandise->pivot->discount_price,
                'event_stock' => $merchandise->pivot->event_stock,
                'is_available' => $merchandise->pivot->is_available,
            ],
        ];
    }
}