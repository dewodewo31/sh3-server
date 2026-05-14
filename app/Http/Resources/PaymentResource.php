<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'payment_method' => $this->payment_method,
            'amount' => $this->amount,
            'amount_formatted' => 'Rp ' . number_format($this->amount, 0, ',', '.'),
            'payment_proof_url' => $this->payment_proof ? asset('storage/' . $this->payment_proof) : null,
            'paid_at' => $this->paid_at ? $this->paid_at->toISOString() : null,
            'status' => $this->status,
            'verified_by' => $this->verifier ? [
                'id' => $this->verifier->id,
                'name' => $this->verifier->name,
            ] : null,
            'verified_at' => $this->verified_at ? $this->verified_at->toISOString() : null,
            'notes' => $this->notes,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}