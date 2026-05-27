<?php

namespace App\Http\Controllers;

use App\Services\DashboardServices\StatisticsService;
use App\Services\DashboardServices\EventService;
use App\Services\DashboardServices\ParticipantService;
use App\Services\DashboardServices\OrderService;
use App\Services\DashboardServices\ChartService;
use App\Models\Event;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $statisticsService;
    protected $eventService;
    protected $participantService;
    protected $orderService;
    protected $chartService;

    public function __construct(
        StatisticsService $statisticsService,
        EventService $eventService,
        ParticipantService $participantService,
        OrderService $orderService,
        ChartService $chartService
    ) {
        $this->statisticsService = $statisticsService;
        $this->eventService = $eventService;
        $this->participantService = $participantService;
        $this->orderService = $orderService;
        $this->chartService = $chartService;
    }

    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            return $this->adminDashboard();
        }
        
        if ($user->role === 'organizer') {
            return $this->organizerDashboard();
        }
        
        return view('dashboard.index');
    }
    
    /**
     * Admin Dashboard - Full access to all data
     */
    private function adminDashboard()
    {
        // Statistics
        $stats = $this->statisticsService->getAdminStatistics();
        
        // Events
        $upcomingEvents = $this->eventService->getUpcomingEvents();
        $ongoingEvents = $this->eventService->getOngoingEvents();
        $historyEvents = $this->eventService->getHistoryEvents();
        $topEvents = $this->eventService->getTopEvents();
        
        // Participants
        $recentParticipants = $this->participantService->getRecentParticipants();
        
        // Orders & Payments
        $recentOrders = $this->orderService->getRecentOrders();
        $pendingPayments = $this->orderService->getPendingPayments();
        
        // ========== TAMBAHKAN ORDER STATUS COUNTS ==========
        $pendingOrders = Order::where('status', 'pending')->count();
        $paidOrders = Order::where('status', 'paid')->count();
        $freeOrders = Order::where('status', 'free')->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();
        
        // Charts
        $participantChart = $this->chartService->getParticipantChartData();
        $revenueChart = $this->chartService->getRevenueChartData();
        
        return view('dashboard.index', array_merge($stats, compact(
            'upcomingEvents',
            'ongoingEvents',
            'historyEvents',
            'recentParticipants',
            'participantChart',
            'revenueChart',
            'topEvents',
            'recentOrders',
            'pendingPayments',
            'pendingOrders',      // Tambahkan
            'paidOrders',         // Tambahkan
            'freeOrders',         // Tambahkan
            'cancelledOrders'     // Tambahkan
        )));
    }
    
    /**
     * Organizer Dashboard - Only their own events data
     */
    private function organizerDashboard()
    {
        $user = Auth::user();
        $eventIds = Event::where('created_by', $user->id)->pluck('id');
        
        // Statistics
        $stats = $this->statisticsService->getOrganizerStatistics($eventIds);
        
        // Events (filtered by organizer)
        $eventsQuery = Event::with(['category'])->where('created_by', $user->id);
        
        $upcomingEvents = $this->eventService->getUpcomingEvents(clone $eventsQuery);
        $ongoingEvents = $this->eventService->getOngoingEvents(clone $eventsQuery);
        $historyEvents = $this->eventService->getHistoryEvents(clone $eventsQuery);
        $topEvents = $this->eventService->getTopEvents(clone $eventsQuery);
        
        // Participants
        $recentParticipants = $this->participantService->getParticipantsForOrganizer($eventIds);
        
        // Orders
        $ordersQuery = Order::with(['participant', 'event'])->whereIn('event_id', $eventIds);
        $recentOrders = $this->orderService->getRecentOrders($ordersQuery);
        
        // ========== TAMBAHKAN ORDER STATUS COUNTS UNTUK ORGANIZER ==========
        $pendingOrders = Order::whereIn('event_id', $eventIds)->where('status', 'pending')->count();
        $paidOrders = Order::whereIn('event_id', $eventIds)->where('status', 'paid')->count();
        $freeOrders = Order::whereIn('event_id', $eventIds)->where('status', 'free')->count();
        $cancelledOrders = Order::whereIn('event_id', $eventIds)->where('status', 'cancelled')->count();
        
        // Charts
        $eventChart = $this->chartService->getEventRegistrationChart($eventIds);
        
        return view('dashboard.organizer', array_merge($stats, compact(
            'upcomingEvents',
            'ongoingEvents',
            'historyEvents',
            'recentParticipants',
            'eventChart',
            'topEvents',
            'recentOrders',
            'pendingOrders',      // Tambahkan untuk organizer
            'paidOrders',         // Tambahkan untuk organizer
            'freeOrders',         // Tambahkan untuk organizer
            'cancelledOrders'     // Tambahkan untuk organizer
        )));
    }
}