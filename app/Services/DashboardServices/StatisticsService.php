<?php

namespace App\Services\DashboardServices;

use App\Models\Event;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Participant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class StatisticsService
{
    /**
     * Get admin dashboard statistics
     */
    public function getAdminStatistics(): array
    {
        return [
            'totalEvents' => Event::count(),
            'totalParticipants' => Participant::count(),
            'totalOrders' => Order::count(),
            'totalRevenue' => Order::where('status', 'paid')->sum('total_price'),
        ];
    }

    /**
     * Get organizer dashboard statistics
     */
    public function getOrganizerStatistics($eventIds): array
    {
        return [
            'totalEvents' => Event::where('created_by', Auth::id())->count(),
            'totalOrders' => Order::whereIn('event_id', $eventIds)->count(),
            'totalRevenue' => Order::whereIn('event_id', $eventIds)->where('status', 'paid')->sum('total_price'),
            'totalParticipants' => Order::whereIn('event_id', $eventIds)->distinct('participant_id')->count('participant_id'),
        ];
    }
}