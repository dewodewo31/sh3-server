@extends('layouts.app')

@section('title', 'Detail Kategori - ' . $category->name)
@section('page-title', 'Category Details')
@section('page-description', 'View category information and related events')

@section('content')
<div class="mb-6">
    <a href="{{ route('categories.index') }}" class="text-gray-400 hover:text-white transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Daftar Kategori
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Category Info -->
    <div class="lg:col-span-1">
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6 sticky top-6">
            <div class="text-center mb-6">
                <div class="w-24 h-24 bg-gradient-to-br from-green-400 to-blue-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white mb-2">{{ $category->name }}</h1>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-green-500/20 rounded-full">
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-green-400 font-semibold">{{ $category->events->count() }} Events</span>
                </div>
            </div>

            <div class="space-y-4 border-t border-white/10 pt-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">ID Kategori</span>
                    <span class="text-white font-mono">#{{ $category->id }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Dibuat Pada</span>
                    <span class="text-white">{{ $category->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Terakhir Update</span>
                    <span class="text-white">{{ $category->updated_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            <div class="flex gap-3 mt-6 pt-4 border-t border-white/10">
                <a href="{{ route('categories.edit', $category) }}" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition text-center">
                    Edit Kategori
                </a>
                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            onclick="return confirm('Yakin ingin menghapus kategori {{ $category->name }}?')"
                            class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition"
                            {{ $category->events->count() > 0 ? 'disabled' : '' }}>
                        Hapus Kategori
                    </button>
                </form>
            </div>
            @if($category->events->count() > 0)
                <p class="text-xs text-red-400 text-center mt-2">
                    * Tidak dapat menghapus kategori yang masih memiliki event
                </p>
            @endif
        </div>
    </div>

    <!-- Related Events -->
    <div class="lg:col-span-2">
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-white">Events in this Category</h2>
                    <p class="text-sm text-gray-400">List of all events under {{ $category->name }}</p>
                </div>
                <a href="{{ route('events.create') }}?category={{ $category->id }}" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-lg text-sm transition">
                    + Tambah Event
                </a>
            </div>

            @if($category->events->count() > 0)
                <div class="space-y-3">
                    @foreach($category->events as $event)
                    <div class="bg-white/5 rounded-lg p-4 hover:bg-white/10 transition group">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    @if($event->image)
                                        <img src="{{ Storage::url($event->image) }}" class="w-12 h-12 rounded-lg object-cover">
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-gray-600 to-gray-700 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="font-semibold text-white group-hover:text-green-400 transition">
                                            {{ $event->title }}
                                        </h3>
                                        <p class="text-xs text-gray-400">
                                            {{ $event->start_date->format('d M Y') }} - {{ $event->end_date->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition">
                                <a href="{{ route('events.show', $event) }}" class="text-blue-400 hover:text-blue-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('events.edit', $event) }}" class="text-yellow-400 hover:text-yellow-300">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-400">Belum ada event dalam kategori ini</p>
                    <a href="{{ route('events.create') }}?category={{ $category->id }}" class="text-green-400 hover:text-green-300 mt-2 inline-block">
                        Buat event pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection