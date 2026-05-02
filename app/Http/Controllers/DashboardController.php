<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Data untuk Admin
        if ($user->role === 'admin') {
            return $this->adminDashboard();
        }
        
        // Data untuk Organizer
        if ($user->role === 'organizer') {
            return $this->organizerDashboard();
        }
        
        // Fallback
        return view('dashboard.index');
    }
    
    /**
     * Admin Dashboard - Full access to all data
     */
    private function adminDashboard()
    {
        // === STATISTICS CARDS ===
        $totalEvents = Event::count();
        $totalParticipants = Participant::count();
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', 'paid')->sum('total_price');
        
        // === UPCOMING EVENTS ===
        $upcomingEvents = Event::with(['category', 'creator'])
            ->where('start_date', '>', now())
            ->orderBy('start_date', 'asc')
            ->take(5)
            ->get()
            ->map(function($event) {
                $registeredCount = $event->orders()->count();
                $remainingQuota = $event->quota - $registeredCount;
                
                return (object) [
                    'id' => $event->id,
                    'title' => $event->title,
                    'date' => $event->start_date,
                    'location' => $event->location,
                    'quota' => $event->quota,
                    'registered' => $registeredCount,
                    'remaining' => $remainingQuota,
                    'percentage' => $event->quota > 0 ? round(($registeredCount / $event->quota) * 100) : 0,
                    'status' => $event->status,
                    'image' => $event->image,
                    'category' => $event->category->name ?? 'Uncategorized'
                ];
            });
        
        // === ONGOING EVENTS ===
        $ongoingEvents = Event::with(['category', 'creator'])
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->take(5)
            ->get()
            ->map(function($event) {
                $registeredCount = $event->orders()->count();
                
                return (object) [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start_date' => $event->start_date,
                    'end_date' => $event->end_date,
                    'location' => $event->location,
                    'registered' => $registeredCount,
                    'remaining_days' => now()->diffInDays($event->end_date) + 1
                ];
            });
        
        // === HISTORY EVENTS (Finished) ===
        $historyEvents = Event::with(['category', 'creator'])
            ->where('end_date', '<', now())
            ->orderBy('end_date', 'desc')
            ->take(5)
            ->get()
            ->map(function($event) {
                $registeredCount = $event->orders()->count();
                $paidCount = $event->orders()->where('status', 'paid')->count();
                $freeCount = $event->orders()->where('status', 'free')->count();
                $revenue = $event->orders()->where('status', 'paid')->sum('total_price');
                
                return (object) [
                    'id' => $event->id,
                    'title' => $event->title,
                    'date' => $event->end_date,
                    'location' => $event->location,
                    'registered' => $registeredCount,
                    'paid_count' => $paidCount,
                    'free_count' => $freeCount,
                    'revenue' => $revenue,
                    'image' => $event->image
                ];
            });
        
        // === RECENT PARTICIPANTS & THEIR EVENTS ===
        $recentParticipants = Participant::with(['orders.event'])
            ->where('status', 'active')
            ->latest()
            ->take(10)
            ->get()
            ->map(function($participant) {
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
            });
        
        // === PARTICIPANT REGISTRATION CHART (Last 6 months) ===
        $participantChart = $this->getParticipantChartData();
        
        // === REVENUE CHART (Last 6 months) ===
        $revenueChart = $this->getRevenueChartData();
        
        // === TOP EVENTS BY REGISTRATION ===
        $topEvents = Event::withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->take(5)
            ->get()
            ->map(function($event) {
                return (object) [
                    'title' => $event->title,
                    'registered' => $event->orders_count,
                    'quota' => $event->quota
                ];
            });
        
        // === RECENT ORDERS ===
        $recentOrders = Order::with(['participant', 'event'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function($order) {
                return (object) [
                    'id' => $order->id,
                    'invoice_number' => $order->invoice_number,
                    'participant_name' => $order->participant->name ?? 'N/A',
                    'event_title' => $order->event->title ?? 'N/A',
                    'total_price' => $order->total_price,
                    'status' => $order->status,
                    'created_at' => $order->created_at
                ];
            });
        
        // === PENDING PAYMENTS ===
        $pendingPayments = Payment::with(['order.participant', 'order.event'])
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($payment) {
                return (object) [
                    'id' => $payment->id,
                    'order_id' => $payment->order_id,
                    'invoice_number' => $payment->order->invoice_number ?? 'N/A',
                    'participant_name' => $payment->order->participant->name ?? 'N/A',
                    'event_title' => $payment->order->event->title ?? 'N/A',
                    'amount' => $payment->amount,
                    'paid_at' => $payment->paid_at
                ];
            });
        
        return view('dashboard.index', compact(
            'totalEvents',
            'totalParticipants',
            'totalOrders',
            'totalRevenue',
            'upcomingEvents',
            'ongoingEvents',
            'historyEvents',
            'recentParticipants',
            'participantChart',
            'revenueChart',
            'topEvents',
            'recentOrders',
            'pendingPayments'
        ));
    }
    
    /**
     * Organizer Dashboard - Only their own events data
     */
    private function organizerDashboard()
    {
        $user = Auth::user();
        $eventIds = Event::where('created_by', $user->id)->pluck('id');
        
        // === STATISTICS CARDS ===
        $totalEvents = Event::where('created_by', $user->id)->count();
        $totalOrders = Order::whereIn('event_id', $eventIds)->count();
        $totalRevenue = Order::whereIn('event_id', $eventIds)->where('status', 'paid')->sum('total_price');
        $totalParticipants = Order::whereIn('event_id', $eventIds)->distinct('participant_id')->count('participant_id');
        
        // === UPCOMING EVENTS (Organizer's events) ===
        $upcomingEvents = Event::with(['category'])
            ->where('created_by', $user->id)
            ->where('start_date', '>', now())
            ->orderBy('start_date', 'asc')
            ->get()
            ->map(function($event) {
                $registeredCount = $event->orders()->count();
                $remainingQuota = $event->quota - $registeredCount;
                
                return (object) [
                    'id' => $event->id,
                    'title' => $event->title,
                    'date' => $event->start_date,
                    'location' => $event->location,
                    'quota' => $event->quota,
                    'registered' => $registeredCount,
                    'remaining' => $remainingQuota,
                    'percentage' => $event->quota > 0 ? round(($registeredCount / $event->quota) * 100) : 0,
                    'status' => $event->status,
                    'image' => $event->image,
                    'category' => $event->category->name ?? 'Uncategorized'
                ];
            });
        
        // === ONGOING EVENTS ===
        $ongoingEvents = Event::where('created_by', $user->id)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->get()
            ->map(function($event) {
                $registeredCount = $event->orders()->count();
                
                return (object) [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start_date' => $event->start_date,
                    'end_date' => $event->end_date,
                    'location' => $event->location,
                    'registered' => $registeredCount,
                    'remaining_days' => now()->diffInDays($event->end_date) + 1
                ];
            });
        
        // === HISTORY EVENTS (Finished) ===
        $historyEvents = Event::where('created_by', $user->id)
            ->where('end_date', '<', now())
            ->orderBy('end_date', 'desc')
            ->get()
            ->map(function($event) {
                $registeredCount = $event->orders()->count();
                $paidCount = $event->orders()->where('status', 'paid')->count();
                $freeCount = $event->orders()->where('status', 'free')->count();
                $revenue = $event->orders()->where('status', 'paid')->sum('total_price');
                
                return (object) [
                    'id' => $event->id,
                    'title' => $event->title,
                    'date' => $event->end_date,
                    'location' => $event->location,
                    'registered' => $registeredCount,
                    'paid_count' => $paidCount,
                    'free_count' => $freeCount,
                    'revenue' => $revenue,
                    'image' => $event->image
                ];
            });
        
        // === RECENT PARTICIPANTS FOR ORGANIZER'S EVENTS ===
        $recentParticipants = Participant::whereHas('orders', function($query) use ($eventIds) {
                $query->whereIn('event_id', $eventIds);
            })
            ->latest()
            ->take(10)
            ->get()
            ->map(function($participant) {
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
            });
        
        // === EVENT REGISTRATION CHART ===
        $eventChart = $this->getEventRegistrationChart($eventIds);
        
        // === TOP EVENTS BY REGISTRATION ===
        $topEvents = Event::where('created_by', $user->id)
            ->withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->take(5)
            ->get()
            ->map(function($event) {
                return (object) [
                    'title' => $event->title,
                    'registered' => $event->orders_count,
                    'quota' => $event->quota
                ];
            });
        
        // === RECENT ORDERS ===
        $recentOrders = Order::with(['participant', 'event'])
            ->whereIn('event_id', $eventIds)
            ->latest()
            ->take(10)
            ->get()
            ->map(function($order) {
                return (object) [
                    'id' => $order->id,
                    'invoice_number' => $order->invoice_number,
                    'participant_name' => $order->participant->name ?? 'N/A',
                    'event_title' => $order->event->title ?? 'N/A',
                    'total_price' => $order->total_price,
                    'status' => $order->status,
                    'created_at' => $order->created_at
                ];
            });
        
        return view('dashboard.organizer', compact(
            'totalEvents',
            'totalOrders',
            'totalRevenue',
            'totalParticipants',
            'upcomingEvents',
            'ongoingEvents',
            'historyEvents',
            'recentParticipants',
            'eventChart',
            'topEvents',
            'recentOrders'
        ));
    }
    
    /**
     * Get participant registration chart data (last 6 months)
     */
    private function getParticipantChartData()
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
    private function getRevenueChartData()
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
    private function getEventRegistrationChart($eventIds)
    {
        $events = Event::whereIn('id', $eventIds)
            ->withCount('orders')
            ->orderBy('start_date', 'asc')
            ->take(6)
            ->get();
        
        return $events->map(function($event) {
            return [
                'title' => $event->title,
                'registered' => $event->orders_count,
                'quota' => $event->quota
            ];
        });
    }
}