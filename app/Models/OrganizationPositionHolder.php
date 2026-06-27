<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationPositionHolder extends Model
{
    protected $table = 'organization_position_holders';
    
    protected $fillable = [
        'hierarchy_id',
        'name',
        'nickname',
        'email',
        'phone',
        'avatar',
        'member_since',
        'bio',
        'achievements',
        'social_media',
        'period_start',
        'period_end',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'social_media' => 'array',
        'is_active' => 'boolean',
        'member_since' => 'integer',
        'period_start' => 'integer',
        'period_end' => 'integer'
    ];

    // Relations
    public function hierarchy()
    {
        return $this->belongsTo(OrganizationHierarchy::class, 'hierarchy_id');
    }

    // Accessor for avatar URL
    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
    }

    // Get display name with nickname
    public function getDisplayNameAttribute()
    {
        return $this->nickname ? "{$this->name} ({$this->nickname})" : $this->name;
    }

    // Get period text
    public function getPeriodTextAttribute()
    {
        if ($this->period_start && $this->period_end) {
            return "{$this->period_start} - {$this->period_end}";
        } elseif ($this->period_start) {
            return "{$this->period_start} - Sekarang";
        }
        return null;
    }
}