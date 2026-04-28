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
            'key_point' => 'nullable|array',
            'key_point.*' => 'string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'category_id' => 'required|exists:categories,id',
            'price' => 'nullable|numeric|min:0',
            'quota' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Judul event wajib diisi',
            'description.required' => 'Deskripsi event wajib diisi',
            'location.required' => 'Lokasi event wajib diisi',
            'start_date.required' => 'Tanggal mulai wajib diisi',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh kurang dari hari ini',
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