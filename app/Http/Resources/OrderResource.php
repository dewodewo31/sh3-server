<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'ticket_code' => $this->ticket_code,
            'total_price' => $this->total_price,
            'total_price_formatted' => $this->total_price > 0 ? 'Rp ' . number_format($this->total_price, 0, ',', '.') : 'GRATIS',
            'status' => $this->status,
            'participant' => new ParticipantResource($this->whenLoaded('participant')),
            'event' => new EventResource($this->whenLoaded('event')),
            'payment' => $this->payment ? new PaymentResource($this->payment) : null,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}