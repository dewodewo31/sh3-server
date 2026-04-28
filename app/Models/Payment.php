<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'payment_method',
        'payment_proof',
        'amount',
        'paid_at',
        'status',
        'verified_by',
        'verified_at',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
    // Relasi ke user yang melakukan verifikasi
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
    
    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
    
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }
    
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}