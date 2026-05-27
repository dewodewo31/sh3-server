<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:500',
            'maps_link' => 'nullable|url',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'key_point' => 'nullable|array',
            'key_point.*' => 'string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'category_id' => 'required|exists:categories,id',
            'price' => 'nullable|numeric|min:0',
            'quota' => 'required|integer|min:1',
            'merchandise_items' => 'nullable|array',
            'merchandise_items.*.merchandise_id' => 'required|exists:merchandise,id',
            'merchandise_items.*.discount_price' => 'nullable|numeric|min:0',
            'merchandise_items.*.event_stock' => 'nullable|integer|min:0',
            'merchandise_items.*.is_available' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Judul event wajib diisi',
            'description.required' => 'Deskripsi event wajib diisi',
            'location.required' => 'Lokasi event wajib diisi',
            'maps_link.url' => 'Link Google Maps tidak valid',
            'start_date.required' => 'Tanggal mulai wajib diisi',
            'end_date.required' => 'Tanggal selesai wajib diisi',
            'end_date.after' => 'Tanggal selesai harus setelah tanggal mulai',
            'category_id.required' => 'Kategori event wajib dipilih',
            'quota.required' => 'Kuota event wajib diisi',
            'quota.min' => 'Kuota minimal 1',
            'image.image' => 'File harus berupa gambar',
            'price.numeric' => 'Harga harus berupa angka',
        ];
    }
}