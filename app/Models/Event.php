<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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