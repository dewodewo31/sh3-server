<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MerchandiseOrder extends Model
{
    protected $table = 'merchandise_orders';
    
    protected $fillable = [
        'participant_id',
        'merchandise_id',
        'invoice_number',
        'quantity',
        'size',
        'color',
        'unit_price',
        'total_price',
        'status',
        'shipping_address',
        'shipping_phone',
        'shipping_courier',
        'tracking_number',
        'paid_at',
        'shipped_at',
        'delivered_at',
        'notes'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime'
    ];

    protected static function booted()
    {
        static::creating(function ($order) {
            if (empty($order->invoice_number)) {
                $order->invoice_number = 'INV-MERCH-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    // Relations
    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function merchandise()
    {
        return $this->belongsTo(Merchandise::class);
    }

    // Status badge colors
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-500/20 text-yellow-300',
            'paid' => 'bg-blue-500/20 text-blue-300',
            'processing' => 'bg-purple-500/20 text-purple-300',
            'shipped' => 'bg-indigo-500/20 text-indigo-300',
            'delivered' => 'bg-green-500/20 text-green-300',
            'cancelled' => 'bg-red-500/20 text-red-300',
            default => 'bg-gray-500/20 text-gray-300',
        };
    }

    // Get formatted total price
    public function getFormattedTotalPriceAttribute()
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }
}