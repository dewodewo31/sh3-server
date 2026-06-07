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
        'quantity',
        'size',
        'color',
        'unit_price',
        'total_price',
        'status',
        'shipping_address',
        'shipping_phone',
        'notes',
        'payment_proof',
        'payment_proof_uploaded_at',
        'paid_amount',
        'payment_method',
        'verified_at',
        'verified_by',
        'verification_notes'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'payment_proof_uploaded_at' => 'datetime',
        'verified_at' => 'datetime',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'paid_amount' => 'decimal:2'
    ];

    protected static function booted()
    {
        static::creating(function ($order) {
            if (empty($order->invoice_number)) {
                $order->invoice_number = 'INV-MD-' . date('Ymd') . '-' . str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
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

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Accessor for payment proof URL
    public function getPaymentProofUrlAttribute()
    {
        return $this->payment_proof ? asset('storage/' . $this->payment_proof) : null;
    }

    // Check if payment is verified
    public function isPaymentVerified()
    {
        return $this->status === 'paid' || $this->verified_at !== null;
    }

    // Get status badge
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="badge badge-warning">Menunggu Pembayaran</span>',
            'paid' => '<span class="badge badge-success">Dibayar</span>',
            'processing' => '<span class="badge badge-info">Diproses</span>',
            'shipped' => '<span class="badge badge-primary">Dikirim</span>',
            'delivered' => '<span class="badge badge-success">Terkirim</span>',
            'cancelled' => '<span class="badge badge-danger">Dibatalkan</span>',
            default => '<span class="badge badge-secondary">Unknown</span>',
        };
    }

    // Tambahkan scope di model MerchandiseOrder
    public function scopePendingPayment($query)
    {
        return $query->where('status', 'pending')
            ->whereNull('payment_proof')
            ->orWhere(function($q) {
                $q->whereNotNull('payment_proof')
                ->whereNull('verified_at');
            });
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // Get formatted total price
    public function getFormattedTotalPriceAttribute()
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }
}