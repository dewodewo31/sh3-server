<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'maps_link' => $this->maps_link,
            'key_points' => $this->key_point,
            'image_url' => $this->image ? asset('storage/' . $this->image) : null,
            'start_date' => $this->start_date->toISOString(),
            'end_date' => $this->end_date->toISOString(),
            'price' => $this->price,
            'price_formatted' => $this->price > 0 ? 'Rp ' . number_format($this->price, 0, ',', '.') : 'GRATIS',
            'quota' => $this->quota,
            'registered_count' => $this->orders()->count(),
            'remaining_quota' => $this->quota - $this->orders()->count(),
            'status' => $this->status,
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ],
            'organizer' => [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
                'email' => $this->creator->email,
            ],
            'sponsors' => $this->whenLoaded('sponsors', function() {
                return [
                    'platinum' => $this->sponsors->where('tier', 'platinum')->values(),
                    'gold' => $this->sponsors->where('tier', 'gold')->values(),
                    'silver' => $this->sponsors->where('tier', 'silver')->values(),
                    'bronze' => $this->sponsors->where('tier', 'bronze')->values(),
                    'partner' => $this->sponsors->where('tier', 'partner')->values(),
                ];
            }),
            'merchandise' => $this->whenLoaded('merchandise', function() {
                return $this->merchandise->map(function($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'slug' => $item->slug,
                        'description' => $item->description,
                        'image_url' => $item->image ? asset('storage/' . $item->image) : null,
                        'category' => $item->category,
                        'sizes' => $item->sizes ?? [],
                        'colors' => $item->colors ?? [],
                        'price' => $item->price,
                        'price_formatted' => 'Rp ' . number_format($item->price, 0, ',', '.'),
                        'event_price' => $item->pivot->discount_price ?? $item->price,
                        'has_discount' => $item->pivot->discount_price !== null && $item->pivot->discount_price < $item->price,
                        'stock' => $item->pivot->event_stock ?? $item->stock,
                        'is_in_stock' => ($item->pivot->event_stock ?? $item->stock) > 0,
                    ];
                });
            }),
            'galleries' => GalleryResource::collection($this->whenLoaded('galleries')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}