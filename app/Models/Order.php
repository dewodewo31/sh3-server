<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'participant_id',
        'event_id',
        'invoice_number',
        'ticket_code',
        'status',
        'total_price'
    ];

    protected $casts = [
        'total_price' => 'decimal:2'
    ];

    protected static function booted()
    {
        static::creating(function ($order) {
            if (!$order->ticket_code) {
                $order->ticket_code = strtoupper(Str::random(10));
            }
            if (!$order->invoice_number) {
                $order->invoice_number = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    // 🔗 RELATIONS - HANYA participant (tidak ada user!)
    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
    
    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
    
    public function scopeFree($query)
    {
        return $query->where('status', 'free');
    }
    
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }
    
    public function attendance()
    {
        return $this->hasOne(Attendance::class);
    }
}