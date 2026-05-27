<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventGalleryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'event_id' => 'required|exists:events,id',
            'images' => 'array|min:1|max:10',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',
            'google_drive_links' => 'nullable|string', // Tambahkan
        ];
    }

    public function messages()
    {
        return [
            'event_id.required' => 'Event wajib dipilih',
            'event_id.exists' => 'Event tidak ditemukan',
            // 'images.required' => 'Minimal upload 1 gambar',
            'images.array' => 'Format images harus array',
            'images.min' => 'Minimal upload :min gambar',
            'images.max' => 'Maksimal upload :max gambar',
            'images.*.image' => 'File harus berupa gambar',
            'images.*.mimes' => 'Format gambar harus jpg, jpeg, atau png',
            'images.*.max' => 'Ukuran gambar maksimal 2MB',
            'google_drive_links.string' => 'Format link Google Drive tidak valid',
        ];
    }
}