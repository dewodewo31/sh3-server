<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Attendance extends Model
{
    protected $fillable = [
        'order_id',
        'event_id',
        'participant_id',
        'qr_code',
        'check_in_time',
        'check_out_time',
        'status',
        'check_in_notes',
        'check_out_notes',
        'check_in_ip',
        'check_out_ip'
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime'
    ];

    protected static function booted()
    {
        static::creating(function ($attendance) {
            if (!$attendance->qr_code) {
                $attendance->qr_code = static::generateQrCode();
            }
        });
    }

    /**
     * Generate unique QR code
     */
    public static function generateQrCode(): string
    {
        do {
            $code = 'ABS-' . strtoupper(Str::random(10));
        } while (self::where('qr_code', $code)->exists());
        
        return $code;
    }

    /**
     * Check if can check out (minimum 15 minutes after check in)
     */
    public function canCheckOut(): bool
    {
        if (!$this->check_in_time) {
            return false;
        }
        
        $minutesDiff = $this->check_in_time->diffInMinutes(now());
        return $minutesDiff >= 15;
    }

    /**
     * Get remaining minutes before can check out
     */
    public function getRemainingMinutesBeforeCheckout(): int
    {
        if (!$this->check_in_time) {
            return 15;
        }
        
        $minutesPassed = $this->check_in_time->diffInMinutes(now());
        $remaining = 15 - $minutesPassed;
        
        return $remaining > 0 ? $remaining : 0;
    }

    // Relations
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}
