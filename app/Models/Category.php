<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name'
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }
    
    // Accessor untuk menghitung total events
    public function getEventsCountAttribute()
    {
        return $this->events()->count();
    }
}