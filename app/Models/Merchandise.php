<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merchandise extends Model
{
    protected $table = 'merchandise';
    
    protected $fillable = [
        'name',
        'description',
        'image',
        'price',
        'stock',
        'category',
        'sizes',
        'colors',
        'is_active',
        'sold_count'
    ];

    protected $casts = [
        'sizes' => 'array',
        'colors' => 'array',
        'price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Relations
    public function orders()
    {
        return $this->hasMany(MerchandiseOrder::class);
    }
    
    // Relation with events
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_merchandise')
            ->withPivot('discount_price', 'event_stock', 'is_available')
            ->withTimestamps();
    }

    // Accessor for image URL
    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    // Get formatted price
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    // Check if in stock
    public function isInStock()
    {
        return $this->stock > 0;
    }

    // Reduce stock
    public function reduceStock($quantity)
    {
        $this->stock -= $quantity;
        $this->sold_count += $quantity;
        $this->save();
    }

    // Scope for active items
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('stock', '>', 0);
    }

    // Scope by category
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}