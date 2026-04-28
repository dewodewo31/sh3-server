<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventGalleryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'event_id' => 'sometimes|exists:events,id',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'event_id.exists' => 'Event tidak ditemukan',
            'images.array' => 'Format images harus array',
            'images.max' => 'Maksimal upload :max gambar',
            'images.*.image' => 'File harus berupa gambar',
            'images.*.mimes' => 'Format gambar harus jpg, jpeg, atau png',
            'images.*.max' => 'Ukuran gambar maksimal 2MB',
        ];
    }
}