<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Attendance;
use Illuminate\Support\Facades\Log;

class AttendanceObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // Hanya untuk order yang sudah paid atau free
        if (in_array($order->status, ['paid', 'free'])) {
            $this->createAttendance($order);
        }
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Jika status berubah menjadi paid atau free, buat attendance jika belum ada
        if (in_array($order->status, ['paid', 'free'])) {
            $attendance = Attendance::where('order_id', $order->id)->first();
            if (!$attendance) {
                $this->createAttendance($order);
            }
        }
    }
    
    /**
     * Create attendance record
     */
    private function createAttendance(Order $order): void
    {
        try {
            Attendance::create([
                'order_id' => $order->id,
                'event_id' => $order->event_id,
                'participant_id' => $order->participant_id,
                'status' => 'pending'
            ]);
            
            Log::info("Attendance created for order #{$order->id}");
        } catch (\Exception $e) {
            Log::error("Failed to create attendance for order #{$order->id}: " . $e->getMessage());
        }
    }
}