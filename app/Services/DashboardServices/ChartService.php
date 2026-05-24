<?php

namespace App\Services\DashboardServices;

use App\Models\Event;
use App\Models\Order;
use App\Models\Participant;
use Carbon\Carbon;

class ChartService
{
    /**
     * Get participant registration chart data (last 6 months)
     */
    public function getParticipantChartData(): array
    {
        $chartData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = Participant::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            
            $chartData[] = [
                'month' => $month->format('M Y'),
                'count' => $count
            ];
        }
        
        return $chartData;
    }

    /**
     * Get revenue chart data (last 6 months)
     */
    public function getRevenueChartData(): array
    {
        $chartData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $revenue = Order::where('status', 'paid')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total_price');
            
            $chartData[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue
            ];
        }
        
        return $chartData;
    }

    /**
     * Get event registration chart for organizer
     */
    public function getEventRegistrationChart($eventIds): array
    {
        $events = Event::whereIn('id', $eventIds)
            ->withCount('orders')
            ->orderBy('start_date', 'asc')
            ->take(6)
            ->get();
        
        return $events->map(fn($event) => [
            'title' => $event->title,
            'registered' => $event->orders_count,
            'quota' => $event->quota
        ])->toArray();
    }
}