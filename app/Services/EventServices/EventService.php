<?php

namespace App\Services\EventServices;

use App\Models\Event;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;
use GuzzleHttp\Client;

class EventService implements EventServiceInterface
{
    protected $event;
    protected $guzzleClient;

    public function __construct(Event $event)
    {
        $this->event = $event;
        $this->guzzleClient = new Client();
    }

    /**
     * Get events with filtering and pagination
     */
    public function getEvents(Request $request): LengthAwarePaginator
    {
        $user = Auth::user();
        $query = Event::with(['category', 'creator']);

        // Organizer hanya lihat event miliknya
        if ($user->role === 'organizer') {
            $query->where('created_by', $user->id);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $this->applyStatusFilter($query, $request->status);
        }

        // Filter by category
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        // Search by title
        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        return $query->latest()->paginate($request->per_page ?? 10);
    }

    /**
     * Get event statistics for dashboard
     */
    public function getEventStats(): array
    {
        $user = Auth::user();
        
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
        
        return compact('totalEvents', 'totalOrders', 'totalRevenue');
    }

    /**
     * Get event by ID
     */
    public function getEventById($id)
    {
        return Event::with(['category', 'creator', 'galleries'])->findOrFail($id);
    }

    /**
     * Get event by slug
     */
    public function getEventBySlug($slug)
    {
        return Event::with(['category', 'creator', 'galleries'])
            ->where('slug', $slug)
            ->firstOrFail();
    }

    /**
     * Create new event
     */
    public function createEvent(array $data)
    {
        // Handle image upload
        if (isset($data['image']) && $data['image']) {
            $data['image'] = $data['image']->store('events', 'public');
        }
        
        // Generate slug
        $data['slug'] = $this->generateSlug($data['title']);
        
        // Get coordinates from address
        if (empty($data['latitude']) || empty($data['longitude'])) {
            $coords = $this->getCoordinatesFromAddress($data['location']);
            if ($coords) {
                $data['latitude'] = $coords['lat'];
                $data['longitude'] = $coords['lng'];
            }
        }
        
        $data['created_by'] = Auth::id();
        
        return Event::create($data);
    }

    /**
     * Update event
     */
    public function updateEvent($id, array $data)
    {
        $event = Event::findOrFail($id);
        
        // Handle image upload
        if (isset($data['image']) && $data['image']) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $data['image'] = $data['image']->store('events', 'public');
        }
        
        // Update slug if title changed
        if (isset($data['title']) && $data['title'] != $event->title) {
            $data['slug'] = $this->generateSlug($data['title']);
        }
        
        // Update coordinates if location changed
        if (isset($data['location']) && $data['location'] != $event->location) {
            $coords = $this->getCoordinatesFromAddress($data['location']);
            if ($coords) {
                $data['latitude'] = $coords['lat'];
                $data['longitude'] = $coords['lng'];
            }
        }
        
        $event->update($data);
        
        return $event;
    }

    /**
     * Delete event
     */
    public function deleteEvent($id)
    {
        $event = Event::findOrFail($id);
        
        // Delete event image
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }
        
        // Delete event galleries
        foreach ($event->galleries as $gallery) {
            $images = $gallery->image;
            if (is_array($images)) {
                foreach ($images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            $gallery->delete();
        }
        
        return $event->delete();
    }

    /**
     * Get coordinates from address using Nominatim (OpenStreetMap)
     */
    public function getCoordinatesFromAddress(string $address): ?array
    {
        if (empty($address)) {
            return null;
        }
        
        try {
            $response = $this->guzzleClient->get('https://nominatim.openstreetmap.org/search', [
                'query' => [
                    'q' => $address,
                    'format' => 'json',
                    'limit' => 1,
                    'addressdetails' => 1
                ],
                'headers' => [
                    'User-Agent' => 'SH3-Event-App/1.0'
                ],
                'timeout' => 10
            ]);
            
            $data = json_decode($response->getBody(), true);
            
            if ($response->getStatusCode() == 200 && count($data) > 0) {
                return [
                    'lat' => (float) $data[0]['lat'],
                    'lng' => (float) $data[0]['lon']
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Geocoding error: ' . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Get upcoming events
     */
    public function getUpcomingEvents(int $limit = 5)
    {
        $user = Auth::user();
        $query = Event::with(['category', 'creator']);
        
        if ($user->role === 'organizer') {
            $query->where('created_by', $user->id);
        }
        
        return $query->where('start_date', '>', now())
            ->orderBy('start_date', 'asc')
            ->take($limit)
            ->get()
            ->map(fn($event) => $this->formatUpcomingEvent($event));
    }

    /**
     * Get ongoing events
     */
    public function getOngoingEvents(int $limit = 5)
    {
        $user = Auth::user();
        $query = Event::with(['category', 'creator']);
        
        if ($user->role === 'organizer') {
            $query->where('created_by', $user->id);
        }
        
        return $query->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->take($limit)
            ->get()
            ->map(fn($event) => $this->formatOngoingEvent($event));
    }

    /**
     * Get history events (finished)
     */
    public function getHistoryEvents(int $limit = 5)
    {
        $user = Auth::user();
        $query = Event::with(['category', 'creator']);
        
        if ($user->role === 'organizer') {
            $query->where('created_by', $user->id);
        }
        
        return $query->where('end_date', '<', now())
            ->orderBy('end_date', 'desc')
            ->take($limit)
            ->get()
            ->map(fn($event) => $this->formatHistoryEvent($event));
    }

    /**
     * Get top events by registration
     */
    public function getTopEvents(int $limit = 5)
    {
        $user = Auth::user();
        $query = Event::query();
        
        if ($user->role === 'organizer') {
            $query->where('created_by', $user->id);
        }
        
        return $query->withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->take($limit)
            ->get()
            ->map(fn($event) => $this->formatTopEvent($event));
    }

    /**
     * Generate slug from title
     */
    private function generateSlug(string $title): string
    {
        $slug = \Illuminate\Support\Str::slug($title);
        $count = Event::where('slug', 'like', $slug . '%')->count();
        
        return $count ? $slug . '-' . ($count + 1) : $slug;
    }

    /**
     * Apply status filter to query
     */
    private function applyStatusFilter($query, string $status)
    {
        if ($status == 'upcoming') {
            $query->where('start_date', '>', now());
        } elseif ($status == 'ongoing') {
            $query->where('start_date', '<=', now())->where('end_date', '>=', now());
        } elseif ($status == 'finished') {
            $query->where('end_date', '<', now());
        }
    }

    /**
     * Format upcoming event data
     */
    private function formatUpcomingEvent($event): object
    {
        $registeredCount = $event->orders()->count();
        
        return (object) [
            'id' => $event->id,
            'title' => $event->title,
            'date' => $event->start_date,
            'location' => $event->location,
            'quota' => $event->quota,
            'registered' => $registeredCount,
            'remaining' => $event->quota - $registeredCount,
            'percentage' => $event->quota > 0 ? round(($registeredCount / $event->quota) * 100) : 0,
            'status' => $event->status,
            'image' => $event->image,
            'category' => $event->category->name ?? 'Uncategorized'
        ];
    }

    /**
     * Format ongoing event data
     */
    private function formatOngoingEvent($event): object
    {
        return (object) [
            'id' => $event->id,
            'title' => $event->title,
            'start_date' => $event->start_date,
            'end_date' => $event->end_date,
            'location' => $event->location,
            'registered' => $event->orders()->count(),
            'remaining_days' => now()->diffInDays($event->end_date) + 1
        ];
    }

    /**
     * Format history event data
     */
    private function formatHistoryEvent($event): object
    {
        return (object) [
            'id' => $event->id,
            'title' => $event->title,
            'date' => $event->end_date,
            'location' => $event->location,
            'registered' => $event->orders()->count(),
            'paid_count' => $event->orders()->where('status', 'paid')->count(),
            'free_count' => $event->orders()->where('status', 'free')->count(),
            'revenue' => $event->orders()->where('status', 'paid')->sum('total_price'),
            'image' => $event->image
        ];
    }

    /**
     * Format top event data
     */
    private function formatTopEvent($event): object
    {
        return (object) [
            'title' => $event->title,
            'registered' => $event->orders_count,
            'quota' => $event->quota
        ];
    }
}