<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
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
            $events = Event::with(['category', 'creator'])
                ->where('created_by', $user->id)
                ->latest()
                ->paginate(10);
        } else {
            // Admin lihat semua
            $events = Event::with(['category', 'creator'])
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
        return view('events.create', compact('categories'));
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

        Event::create($data);

        return redirect()->route('events.index')
            ->with('success', 'Event berhasil dibuat');
    }

    /**
     * Show detail event
     */
    public function show(Event $event)
    {
        $this->authorizeEvent($event);
        
        $event->load(['category', 'creator', 'galleries']);
        
        return view('events.show', compact('event'));
    }

    /**
     * Show form edit
     */
    public function edit(Event $event)
    {
        $this->authorizeEvent($event);

        $categories = Category::all();

        return view('events.edit', compact('event', 'categories'));
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