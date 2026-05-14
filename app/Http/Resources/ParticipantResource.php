<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticipantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'hash_id' => $this->hash_id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'gender' => $this->gender,
            'birthdate' => $this->birthdate->toISOString(),
            'age' => $this->birthdate->age,
            'photo_url' => $this->photo ? asset('storage/' . $this->photo) : null,
            'status' => $this->status,
            'total_orders' => $this->orders()->count(),
            'total_spent' => $this->orders()->where('status', 'paid')->sum('total_price'),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}