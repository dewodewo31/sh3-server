<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Sponsor extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'website',
        'email',
        'phone',
        'description',
        'year',
        'tier', // Default tier (fallback)
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    protected static function booted()
    {
        static::creating(function ($sponsor) {
            if (empty($sponsor->slug)) {
                $sponsor->slug = Str::slug($sponsor->name);
            }
        });
    }

    // Relation with events with pivot data
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_sponsor')
            ->withPivot('tier', 'sponsorship_level', 'contribution_amount', 'benefits', 'sort_order')
            ->withTimestamps();
    }

    // Get specific sponsor data for an event
    public function getEventSponsorData($eventId)
    {
        $pivot = $this->events()->where('event_id', $eventId)->first();
        
        if ($pivot) {
            return [
                'tier' => $pivot->pivot->tier ?? $this->tier,
                'sponsorship_level' => $pivot->pivot->sponsorship_level,
                'contribution_amount' => $pivot->pivot->contribution_amount,
                'benefits' => $pivot->pivot->benefits,
                'sort_order' => $pivot->pivot->sort_order
            ];
        }
        
        return [
            'tier' => $this->tier,
            'sponsorship_level' => null,
            'contribution_amount' => null,
            'benefits' => null,
            'sort_order' => $this->sort_order
        ];
    }

    // Scope for active sponsors
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope by default tier
    public function scopeByTier($query, $tier)
    {
        return $query->where('tier', $tier);
    }

    // Get logo URL
    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    // Get tier badge color (default)
    public function getTierBadgeAttribute()
    {
        return $this->getTierBadgeForTier($this->tier);
    }
    
    // Get tier badge for specific tier
    public function getTierBadgeForTier($tier)
    {
        return match($tier) {
            'platinum' => 'bg-purple-500/20 text-purple-300',
            'gold' => 'bg-yellow-500/20 text-yellow-300',
            'silver' => 'bg-gray-400/20 text-gray-300',
            'bronze' => 'bg-orange-500/20 text-orange-300',
            default => 'bg-blue-500/20 text-blue-300',
        };
    }
    
    // Available tiers
    public static function getAvailableTiers()
    {
        return ['platinum', 'gold', 'silver', 'bronze', 'partner'];
    }
}