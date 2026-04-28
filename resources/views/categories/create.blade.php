@extends('layouts.app')

@section('title', 'Tambah Kategori')
@section('page-title', 'Create New Category')
@section('page-description', 'Add a new event category')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('categories.index') }}" class="text-gray-400 hover:text-white transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Kategori
        </a>
    </div>

    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-8">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-6 bg-gradient-to-br from-green-400 to-blue-500 rounded-xl flex items-center justify-center">
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white">Form Tambah Kategori</h2>
                <p class="text-sm text-gray-400">Isikan data kategori baru</p>
            </div>
        </div>

        <form action="{{ route('categories.store') }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-gray-300 mb-2 font-semibold">Nama Kategori *</label>
                <input type="text" 
                       name="name" 
                       value="{{ old('name') }}"
                       class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition @error('name') border-red-500 @enderror"
                       placeholder="Contoh: Long Run, Short Run"
                       autofocus>
                <p class="text-xs text-gray-400 mt-1">Nama kategori harus unik dan maksimal 255 karakter</p>
                @error('name')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition font-semibold">
                    Simpan Kategori
                </button>
                <a href="{{ route('categories.index') }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection