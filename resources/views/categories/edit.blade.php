@extends('layouts.app')

@section('title', 'Edit Kategori')
@section('page-title', 'Edit Category')
@section('page-description', 'Update category information')

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
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white">Edit Kategori</h2>
                <p class="text-sm text-gray-400">Ubah data kategori yang sudah ada</p>
            </div>
        </div>

        <form action="{{ route('categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label class="block text-gray-300 mb-2 font-semibold">Nama Kategori *</label>
                <input type="text" 
                       name="name" 
                       value="{{ old('name', $category->name) }}"
                       class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-500/20 transition @error('name') border-red-500 @enderror"
                       placeholder="Contoh: Workshop, Seminar, Conference, dll">
                <p class="text-xs text-gray-400 mt-1">Nama kategori harus unik dan maksimal 255 karakter</p>
                @error('name')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category Info Card -->
            <div class="bg-white/5 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-400">ID Kategori</p>
                        <p class="text-white font-semibold">#{{ $category->id }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Total Events</p>
                        <p class="text-green-400 font-semibold">{{ $category->events_count ?? $category->events()->count() }} Event</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Dibuat Pada</p>
                        <p class="text-white">{{ $category->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Terakhir Update</p>
                        <p class="text-white">{{ $category->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg transition font-semibold">
                    Update Kategori
                </button>
                <a href="{{ route('categories.index') }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection