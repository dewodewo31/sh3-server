<?php

namespace App\Services\DashboardServices;

use App\Models\Participant;
use App\Models\Order;

class ParticipantService
{
    /**
     * Get recent participants with their events
     */
    public function getRecentParticipants($query = null, $limit = 10)
    {
        $query = $query ?? Participant::query();
        
        return $query->with(['orders.event'])
            ->where('status', 'active')
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn($participant) => $this->formatRecentParticipant($participant));
    }

    /**
     * Get participants for organizer events
     */
    public function getParticipantsForOrganizer($eventIds, $limit = 10)
    {
        return Participant::whereHas('orders', function($query) use ($eventIds) {
                $query->whereIn('event_id', $eventIds);
            })
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn($participant) => $this->formatRecentParticipant($participant));
    }

    /**
     * Format recent participant data
     */
    private function formatRecentParticipant($participant)
    {
        $lastOrder = $participant->orders()->latest()->first();
        
        return (object) [
            'id' => $participant->id,
            'name' => $participant->name,
            'email' => $participant->email,
            'hash_id' => $participant->hash_id,
            'registered_at' => $participant->created_at,
            'total_orders' => $participant->orders()->count(),
            'last_event' => $lastOrder ? $lastOrder->event->title : 'Belum ada',
            'last_event_date' => $lastOrder ? $lastOrder->created_at : null,
            'status' => $participant->status
        ];
    }
}