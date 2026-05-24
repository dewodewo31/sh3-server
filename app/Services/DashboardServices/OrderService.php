<?php

namespace App\Services\DashboardServices;

use App\Models\Order;
use App\Models\Payment;

class OrderService
{
    /**
     * Get recent orders
     */
    public function getRecentOrders($query = null, $limit = 10)
    {
        $query = $query ?? Order::with(['participant', 'event']);
        
        return $query->latest()
            ->take($limit)
            ->get()
            ->map(fn($order) => $this->formatRecentOrder($order));
    }

    /**
     * Get pending payments
     */
    public function getPendingPayments($limit = 5)
    {
        return Payment::with(['order.participant', 'order.event'])
            ->where('status', 'pending')
            ->latest()
            ->take($limit)
            ->get()
            ->map(fn($payment) => $this->formatPendingPayment($payment));
    }

    /**
     * Format recent order data
     */
    private function formatRecentOrder($order)
    {
        return (object) [
            'id' => $order->id,
            'invoice_number' => $order->invoice_number,
            'participant_name' => $order->participant->name ?? 'N/A',
            'event_title' => $order->event->title ?? 'N/A',
            'total_price' => $order->total_price,
            'status' => $order->status,
            'created_at' => $order->created_at
        ];
    }

    /**
     * Format pending payment data
     */
    private function formatPendingPayment($payment)
    {
        return (object) [
            'id' => $payment->id,
            'order_id' => $payment->order_id,
            'invoice_number' => $payment->order->invoice_number ?? 'N/A',
            'participant_name' => $payment->order->participant->name ?? 'N/A',
            'event_title' => $payment->order->event->title ?? 'N/A',
            'amount' => $payment->amount,
            'paid_at' => $payment->paid_at
        ];
    }
}