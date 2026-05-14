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
            'galleries' => GalleryResource::collection($this->whenLoaded('galleries')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}