<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GalleryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'images' => collect($this->image)->map(function($image) {
                return asset('storage/' . $image);
            }),
            'uploaded_by' => [
                'id' => $this->uploader->id,
                'name' => $this->uploader->name,
            ],
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}