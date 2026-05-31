<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use App\Models\Merchandise;
use App\Models\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;

class EventController extends Controller
{
    /**
     * Display a listing of events
     */
    public function index()
    {
        $user = Auth::user();

        // Organizer hanya lihat event miliknya
        if ($user->role === 'organizer') {
            $events = Event::with(['category', 'creator', 'sponsors'])
                ->where('created_by', $user->id)
                ->latest()
                ->paginate(10);
        } else {
            // Admin lihat semua
            $events = Event::with(['category', 'creator', 'merchandise', 'sponsors'])
                ->latest()
                ->paginate(10);
        }

        // Stats for dashboard
        $totalEvents = Event::when($user->role === 'organizer', function($q) use ($user) {
            return $q->where('created_by', $user->id);
        })->count();

        $totalOrders = Event::when($user->role === 'organizer', function($q) use ($user) {
            return $q->where('created_by', $user->id);
        })->withCount('orders')->get()->sum('orders_count');

        $totalRevenue = Event::when($user->role === 'organizer', function($q) use ($user) {
            return $q->where('created_by', $user->id);
        })->with('orders')->get()->sum(function($event) {
            return $event->orders->sum('total_price');
        });

        return view('events.index', compact('events', 'totalEvents', 'totalOrders', 'totalRevenue'));
    }

    /**
     * Show form create
     */
    public function create()
    {
        $categories = Category::all();
        $merchandise = Merchandise::active()->get();
        $sponsors = Sponsor::where('is_active', true)->orderBy('sort_order')->get();
        
        return view('events.create', compact('categories', 'merchandise', 'sponsors'));
    }

    /**
     * Store new event
     */
    public function store(StoreEventRequest $request)
    {
        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $data['created_by'] = Auth::id();

        $event = Event::create($data);

        // Attach merchandise items to event
        if ($request->has('merchandise_items')) {
            foreach ($request->merchandise_items as $item) {
                $event->merchandise()->attach($item['merchandise_id'], [
                    'discount_price' => $item['discount_price'] ?? null,
                    'event_stock' => $item['event_stock'] ?? null,
                    'is_available' => $item['is_available'] ?? true,
                ]);
            }
        }

        // Attach sponsors with event-specific tier
        if ($request->has('sponsors')) {
            foreach ($request->sponsors as $sponsorData) {
                if (!empty($sponsorData['sponsor_id'])) {
                    $event->sponsors()->attach($sponsorData['sponsor_id'], [
                        'tier' => $sponsorData['tier'] ?? null,
                        'contribution_amount' => $sponsorData['contribution_amount'] ?? null,
                        'benefits' => $sponsorData['benefits'] ?? null,
                        'sort_order' => $sponsorData['sort_order'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('events.index')
            ->with('success', 'Event berhasil dibuat');
    }

    /**
     * Show detail event
     */
    public function show(Event $event)
    {
        $this->authorizeEvent($event);

        // Load all necessary relationships
        $event->load([
            'category', 
            'creator', 
            'galleries', 
            'merchandise',
            'sponsors' => function($query) {
                $query->where('is_active', true)
                      ->orderBy('event_sponsor.sort_order')
                      ->orderBy('sponsors.sort_order');
            }
        ]);

        // Get sponsors grouped by tier for display
        $sponsorsByTier = [
            'platinum' => [],
            'gold' => [],
            'silver' => [],
            'bronze' => [],
            'partner' => []
        ];

        foreach ($event->sponsors as $sponsor) {
            $tier = $sponsor->pivot->tier ?? $sponsor->tier;
            if (isset($sponsorsByTier[$tier])) {
                $sponsorsByTier[$tier][] = $sponsor;
            }
        }

        return view('events.show', compact('event', 'sponsorsByTier'));
    }

    /**
     * Show form edit
     */
    public function edit(Event $event)
    {
        $this->authorizeEvent($event);

        $categories = Category::all();
        $merchandise = Merchandise::active()->get();
        $sponsors = Sponsor::where('is_active', true)->orderBy('sort_order')->get();
        
        // Load existing relationships
        $event->load(['merchandise', 'sponsors']);
        
        // Prepare selected sponsors with their pivot data
        $selectedSponsors = [];
        foreach ($event->sponsors as $sponsor) {
            $selectedSponsors[] = [
                'sponsor_id' => $sponsor->id,
                'tier' => $sponsor->pivot->tier,
                'contribution_amount' => $sponsor->pivot->contribution_amount,
                'benefits' => $sponsor->pivot->benefits,
                'sort_order' => $sponsor->pivot->sort_order,
            ];
        }

        return view('events.edit', compact('event', 'categories', 'merchandise', 'sponsors', 'selectedSponsors'));
    }

    /**
     * Update event
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        $this->authorizeEvent($event);

        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $event->update($data);

        // Sync merchandise items
        if ($request->has('merchandise_items')) {
            $syncData = [];
            foreach ($request->merchandise_items as $item) {
                $syncData[$item['merchandise_id']] = [
                    'discount_price' => $item['discount_price'] ?? null,
                    'event_stock' => $item['event_stock'] ?? null,
                    'is_available' => $item['is_available'] ?? true,
                ];
            }
            $event->merchandise()->sync($syncData);
        } else {
            // If no merchandise items, remove all
            $event->merchandise()->detach();
        }

        // Sync sponsors with event-specific tier
        if ($request->has('sponsors')) {
            $sponsorSyncData = [];
            foreach ($request->sponsors as $sponsorData) {
                if (!empty($sponsorData['sponsor_id'])) {
                    $sponsorSyncData[$sponsorData['sponsor_id']] = [
                        'tier' => $sponsorData['tier'] ?? null,
                        'contribution_amount' => $sponsorData['contribution_amount'] ?? null,
                        'benefits' => $sponsorData['benefits'] ?? null,
                        'sort_order' => $sponsorData['sort_order'] ?? 0,
                    ];
                }
            }
            $event->sponsors()->sync($sponsorSyncData);
        } else {
            // If no sponsors, remove all
            $event->sponsors()->detach();
        }

        return redirect()->route('events.index')
            ->with('success', 'Event berhasil diupdate');
    }

    /**
     * Delete event
     */
    public function destroy(Event $event)
    {
        $this->authorizeEvent($event);

        // Delete event image
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        // Detach all merchandise relations
        $event->merchandise()->detach();
        
        // Detach all sponsor relations
        $event->sponsors()->detach();

        $event->delete();

        return redirect()->route('events.index')
            ->with('success', 'Event berhasil dihapus');
    }

    /**
     * 🔒 Authorization helper
     */
    private function authorizeEvent(Event $event)
    {
        $user = Auth::user();

        // Organizer only can access their own events
        if ($user->role === 'organizer' && $event->created_by !== $user->id) {
            abort(403, 'Tidak diizinkan');
        }
    }
}