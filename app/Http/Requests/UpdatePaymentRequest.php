<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payment_method' => 'required|string|max:100',

            'payment_proof' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'amount' => 'required|numeric|min:0',

            'status' => 'required|in:pending,confirmed,rejected',

            'paid_at' => 'nullable|date'
        ];
    }
}
