<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'location' => 'sometimes|required|string|max:500',
            'maps_link' => 'nullable|url',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'key_point' => 'nullable|array',
            'key_point.*' => 'string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after:start_date',
            'category_id' => 'sometimes|required|exists:categories,id',
            'price' => 'nullable|numeric|min:0',
            'quota' => 'sometimes|required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'end_date.after' => 'Tanggal selesai harus setelah tanggal mulai',
        ];
    }
}