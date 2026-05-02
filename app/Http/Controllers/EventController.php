<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Barryvdh\DomPDF\Facade\Pdf;

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

     /**
     * Export single event to PDF (Brochure)
     */
    public function exportBrochurePdf(Event $event)
    {
        $this->authorizeEvent($event);
        
        $event->load(['category', 'creator', 'galleries']);
        $registeredCount = $event->orders()->count();
        $remainingQuota = $event->quota - $registeredCount;
        
        $pdf = Pdf::loadView('exports.event-brochure', compact('event', 'registeredCount', 'remainingQuota'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('event_' . $event->slug . '.pdf');
    }

    /**
     * Export all events to PDF
     */
    public function exportAllPdf(Request $request)
    {
        $user = Auth::user();
        $query = Event::with(['category', 'creator']);
        
        // Filter untuk organizer
        if ($user->role === 'organizer') {
            $query->where('created_by', $user->id);
        }
        
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
        
        $events = $query->latest()->get();
        
        // Stats
        $totalEvents = $events->count();
        $upcomingCount = $events->filter(fn($e) => $e->status === 'upcoming')->count();
        $ongoingCount = $events->filter(fn($e) => $e->status === 'ongoing')->count();
        $finishedCount = $events->filter(fn($e) => $e->status === 'finished')->count();
        $totalParticipants = $events->sum(fn($e) => $e->orders()->count());
        $totalRevenue = $events->sum(fn($e) => $e->orders()->where('status', 'paid')->sum('total_price'));
        
        $pdf = Pdf::loadView('exports.events-all', compact(
            'events',
            'totalEvents',
            'upcomingCount',
            'ongoingCount',
            'finishedCount',
            'totalParticipants',
            'totalRevenue'
        ));
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('events_' . date('Y-m-d') . '.pdf');
    }
}