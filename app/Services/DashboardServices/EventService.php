<?php

namespace App\Services\DashboardServices;

use App\Models\Event;
use Carbon\Carbon;

class EventService
{
    /**
     * Get upcoming events
     */
    public function getUpcomingEvents($query = null, $limit = 5)
    {
        $query = $query ?? Event::with(['category', 'creator']);
        
        return $query->where('start_date', '>', now())
            ->orderBy('start_date', 'asc')
            ->take($limit)
            ->get()
            ->map(fn($event) => $this->formatUpcomingEvent($event));
    }

    /**
     * Get ongoing events
     */
    public function getOngoingEvents($query = null, $limit = 5)
    {
        $query = $query ?? Event::with(['category', 'creator']);
        
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
    public function getHistoryEvents($query = null, $limit = 5)
    {
        $query = $query ?? Event::with(['category', 'creator']);
        
        return $query->where('end_date', '<', now())
            ->orderBy('end_date', 'desc')
            ->take($limit)
            ->get()
            ->map(fn($event) => $this->formatHistoryEvent($event));
    }

    /**
     * Get top events by registration
     */
    public function getTopEvents($query = null, $limit = 5)
    {
        $query = $query ?? Event::query();
        
        return $query->withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->take($limit)
            ->get()
            ->map(fn($event) => $this->formatTopEvent($event));
    }

    /**
     * Format upcoming event data
     */
    private function formatUpcomingEvent($event)
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
    private function formatOngoingEvent($event)
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
    private function formatHistoryEvent($event)
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
    private function formatTopEvent($event)
    {
        return (object) [
            'title' => $event->title,
            'registered' => $event->orders_count,
            'quota' => $event->quota
        ];
    }
}