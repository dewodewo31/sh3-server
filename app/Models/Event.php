<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'location',
        'key_point',
        'image',
        'start_date',
        'end_date',
        'category_id',
        'price',
        'quota',
        'created_by',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'key_point' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'price' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // 🔗 RELATION
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function galleries()
    {
        return $this->hasMany(EventGallery::class);
    }

    // RELATION WITH MERCHANDISE (Many-to-Many)
    public function merchandise()
    {
        return $this->belongsToMany(Merchandise::class, 'event_merchandise')
            ->withPivot('discount_price', 'event_stock', 'is_available')
            ->withTimestamps();
    }

    // Get available merchandise for this event
    public function getAvailableMerchandise()
    {
        return $this->merchandise()
            ->wherePivot('is_available', true)
            ->where(function($q) {
                $q->whereNull('event_stock')->orWhere('event_stock', '>', 0);
            })
            ->get();
    }

    // Get merchandise with custom price
    public function getMerchandisePrice(Merchandise $merchandise)
    {
        $pivot = $this->merchandise()->where('merchandise_id', $merchandise->id)->first();
        
        if ($pivot && $pivot->pivot->discount_price) {
            return $pivot->pivot->discount_price;
        }
        
        return $merchandise->price;
    }

    // Get merchandise stock for this event
    public function getMerchandiseStock(Merchandise $merchandise)
    {
        $pivot = $this->merchandise()->where('merchandise_id', $merchandise->id)->first();
        
        if ($pivot && $pivot->pivot->event_stock !== null) {
            return $pivot->pivot->event_stock;
        }
        
        return $merchandise->stock;
    }

    public function getStatusAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return 'unknown';
        }

        if (now()->lt($this->start_date)) return 'upcoming';
        if (now()->gt($this->end_date)) return 'finished';

        return 'ongoing';
    }
    
    // Accessor untuk status badge
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'upcoming' => '<span class="badge badge-warning">Akan Datang</span>',
            'ongoing' => '<span class="badge badge-success">Berlangsung</span>',
            'finished' => '<span class="badge badge-secondary">Selesai</span>',
            default => '<span class="badge badge-dark">Unknown</span>',
        };
    }

    // Relation with sponsors
    public function sponsors()
    {
        return $this->belongsToMany(Sponsor::class, 'event_sponsor')
            ->withPivot('sponsorship_level', 'contribution_amount', 'benefits')
            ->withTimestamps();
    }

    // Get sponsors by tier
    public function getSponsorsByTier($tier)
    {
        return $this->sponsors()->where('tier', $tier)->get();
    }
}