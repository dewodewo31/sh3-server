<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Order;
use App\Models\Participant;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Data untuk statistik
        $data = [
            'totalEvents' => Event::count(),
            'totalParticipants' => Participant::count(),
            'totalOrders' => Order::count(),
            'totalRevenue' => Payment::where('status', 'verified')->sum('amount') ?? 0,
            'pendingOrders' => Order::where('status', 'pending')->count(),
            'paidOrders' => Order::where('status', 'paid')->count(),
            'freeOrders' => Order::where('status', 'free')->count(),
            'cancelledOrders' => Order::where('status', 'cancelled')->count(),
            'pendingPayments' => Payment::where('status', 'pending')->count(),
        ];

        // Data untuk chart - PASTIKAN INI ARRAY
        $data['participantChart'] = $this->getParticipantChartData();
        $data['revenueChart'] = $this->getRevenueChartData();
        
        // Data untuk top events
        $data['topEvents'] = Event::withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->limit(5)
            ->get()
            ->map(function($event) {
                return [
                    'title' => $event->title,
                    'quota' => $event->quota,
                    'registered' => $event->orders_count,
                    'status' => $event->status,
                ];
            });

        // Data untuk recent orders
        $data['recentOrders'] = Order::with(['participant', 'event'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($order) {
                return [
                    'participant_name' => $order->participant->name ?? 'N/A',
                    'event_title' => $order->event->title ?? 'N/A',
                    'total_price' => $order->total_price,
                    'status' => $order->status,
                ];
            });

        // Data untuk pending payments
        $data['pendingPayments'] = Payment::with(['order.participant', 'order.event'])
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($payment) {
                return [
                    'participant_name' => $payment->order->participant->name ?? 'N/A',
                    'event_title' => $payment->order->event->title ?? 'N/A',
                    'invoice_number' => $payment->order->invoice_number ?? 'N/A',
                    'amount' => $payment->amount,
                    'order_id' => $payment->order_id,
                ];
            });

        // Jika tidak ada data, set empty collection
        if ($data['pendingPayments']->isEmpty()) {
            $data['pendingPayments'] = collect();
        }

        return view('dashboard.index', $data);
    }

    private function getParticipantChartData()
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $data[] = [
                'month' => $month->format('M Y'),
                'count' => Participant::whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->count(),
            ];
        }
        return $data;
    }

    private function getRevenueChartData()
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $data[] = [
                'month' => $month->format('M Y'),
                'revenue' => Payment::whereMonth('created_at', $month->month)
                    ->whereYear('created_at', $month->year)
                    ->where('status', 'verified')
                    ->sum('amount') ?? 0,
            ];
        }
        return $data;
    }
    
}