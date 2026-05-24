<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\EventServices\EventServiceInterface;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    protected $eventService;

    public function __construct(EventServiceInterface $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * Display a listing of events
     */
    public function index(Request $request)
    {
        $events = $this->eventService->getEvents($request);
        $stats = $this->eventService->getEventStats();
        
        return view('events.index', array_merge(
            compact('events'),
            $stats
        ));
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
        $this->eventService->createEvent($request->validated());

        return redirect()->route('events.index')
            ->with('success', 'Event berhasil dibuat');
    }

    /**
     * Show detail event
     */
    public function show($id)
    {
        $event = $this->eventService->getEventById($id);
        $this->authorizeEvent($event);
        
        return view('events.show', compact('event'));
    }

    /**
     * Show form edit
     */
    public function edit($id)
    {
        $event = $this->eventService->getEventById($id);
        $this->authorizeEvent($event);
        
        $categories = Category::all();

        return view('events.edit', compact('event', 'categories'));
    }

    /**
     * Update event
     */
    public function update(UpdateEventRequest $request, $id)
    {
        $event = $this->eventService->getEventById($id);
        $this->authorizeEvent($event);
        
        $this->eventService->updateEvent($id, $request->validated());

        return redirect()->route('events.index')
            ->with('success', 'Event berhasil diupdate');
    }

    /**
     * Delete event
     */
    public function destroy($id)
    {
        $event = $this->eventService->getEventById($id);
        $this->authorizeEvent($event);
        
        $this->eventService->deleteEvent($id);

        return redirect()->route('events.index')
            ->with('success', 'Event berhasil dihapus');
    }

    /**
     * Export single event to PDF (Brochure)
     */
    public function exportBrochurePdf($id)
    {
        $event = $this->eventService->getEventById($id);
        $this->authorizeEvent($event);
        
        $registeredCount = $event->orders()->count();
        $remainingQuota = $event->quota - $registeredCount;
        
        $pdf = Pdf::loadView('exports.event-brochure', compact('event', 'registeredCount', 'remainingQuota'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('event_' . $event->id . '.pdf');
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

    /**
     * 🔒 Authorization helper
     */
    private function authorizeEvent($event)
    {
        $user = Auth::user();

        // Organizer only can access their own events
        if ($user->role === 'organizer' && $event->created_by !== $user->id) {
            abort(403, 'Tidak diizinkan');
        }
    }
}