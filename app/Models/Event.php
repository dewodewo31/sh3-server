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

    // RELATION WITH SPONSORS (Many-to-Many with event-specific data)
    public function sponsors()
    {
        return $this->belongsToMany(Sponsor::class, 'event_sponsor')
            ->withPivot('tier', 'sponsorship_level', 'contribution_amount', 'benefits', 'sort_order')
            ->withTimestamps();
    }

    // Get sponsors grouped by their tier for this event
    public function getSponsorsGroupedByTierAttribute()
    {
        $sponsors = $this->sponsors()
            ->where('is_active', true)
            ->orderBy('pivot_sort_order')
            ->get();
        
        return $sponsors->groupBy(function($sponsor) {
            return $sponsor->pivot->tier ?? $sponsor->tier;
        });
    }

    // Get sponsors by specific tier for this event
    public function getSponsorsByTier($tier)
    {
        return $this->sponsors()
            ->where('is_active', true)
            ->where(function($query) use ($tier) {
                $query->where('event_sponsor.tier', $tier)
                      ->orWhere(function($q) use ($tier) {
                          $q->whereNull('event_sponsor.tier')
                            ->where('sponsors.tier', $tier);
                      });
            })
            ->orderBy('event_sponsor.sort_order')
            ->get();
    }

    // Get all active sponsors with their event-specific data
    public function getActiveSponsorsWithPivotAttribute()
    {
        return $this->sponsors()
            ->where('is_active', true)
            ->orderBy('event_sponsor.sort_order')
            ->get()
            ->map(function($sponsor) {
                return (object) [
                    'id' => $sponsor->id,
                    'name' => $sponsor->name,
                    'logo' => $sponsor->logo,
                    'logo_url' => $sponsor->logo_url,
                    'website' => $sponsor->website,
                    'description' => $sponsor->description,
                    'tier' => $sponsor->pivot->tier ?? $sponsor->tier,
                    'contribution_amount' => $sponsor->pivot->contribution_amount,
                    'benefits' => $sponsor->pivot->benefits,
                    'sort_order' => $sponsor->pivot->sort_order ?? $sponsor->sort_order,
                ];
            });
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
}