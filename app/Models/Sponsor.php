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
        'tier',
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

    // Relation with events (many-to-many)
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_sponsor')
            ->withPivot('sponsorship_level', 'contribution_amount', 'benefits')
            ->withTimestamps();
    }

    // Scope for active sponsors
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope by tier
    public function scopeByTier($query, $tier)
    {
        return $query->where('tier', $tier);
    }

    // Get logo URL
    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    // Get tier badge color
    public function getTierBadgeAttribute()
    {
        return match($this->tier) {
            'platinum' => 'bg-purple-500/20 text-purple-300',
            'gold' => 'bg-yellow-500/20 text-yellow-300',
            'silver' => 'bg-gray-400/20 text-gray-300',
            'bronze' => 'bg-orange-500/20 text-orange-300',
            default => 'bg-blue-500/20 text-blue-300',
        };
    }
}