@extends('layouts.app')

@section('title', 'Category Management')
@section('page-title', 'Categories')
@section('page-description', 'Manage all event categories')

@section('stats')
    <div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/>
            </svg>
            <p class="text-gray-400 text-sm">Total Categories</p>
        </div>
        <h3 class="text-2xl font-bold text-green-400">{{ $totalCategories ?? 0 }}</h3>
    </div>

    <div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <p class="text-gray-400 text-sm">Categories with Events</p>
        </div>
        <h3 class="text-2xl font-bold text-green-400">{{ $totalEventsWithCategories ?? 0 }}</h3>
    </div>

    <div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-400 text-sm">Total Events</p>
        </div>
        <h3 class="text-2xl font-bold text-green-400">{{ $categories->sum('events_count') ?? 0 }}</h3>
    </div>
@endsection

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-white">Category Management</h2>
    <a href="{{ route('categories.create') }}" 
       class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Kategori
    </a>
</div>

@if(session('success'))
    <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-4 alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-4 alert-error">
        {{ session('error') }}
    </div>
@endif

<!-- Categories Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($categories as $category)
    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 hover:border-green-500/50 transition-all duration-300 overflow-hidden group">
        <div class="p-6">
            <!-- Category Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white group-hover:text-green-400 transition">
                            {{ $category->name }}
                        </h3>
                        <p class="text-sm text-gray-400">
                            ID: #{{ $category->id }}
                        </p>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <a href="{{ route('categories.show', $category) }}" 
                       class="p-2 bg-blue-500/20 hover:bg-blue-500/30 rounded-lg text-blue-400 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                    <a href="{{ route('categories.edit', $category) }}" 
                       class="p-2 bg-yellow-500/20 hover:bg-yellow-500/30 rounded-lg text-yellow-400 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Yakin ingin menghapus kategori {{ $category->name }}?')"
                                class="p-2 bg-red-500/20 hover:bg-red-500/30 rounded-lg text-red-400 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Category Stats -->
            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/10">
                <div class="text-center">
                    <p class="text-2xl font-bold text-green-400">{{ $category->events_count }}</p>
                    <p class="text-xs text-gray-400">Total Events</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-blue-400">{{ $category->created_at->format('d/m/Y') }}</p>
                    <p class="text-xs text-gray-400">Created Date</p>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full">
        <div class="text-center py-12">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/>
            </svg>
            <p class="text-gray-400 text-lg mb-4">Belum ada kategori</p>
            <a href="{{ route('categories.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Kategori Pertama
            </a>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($categories->hasPages())
    <div class="mt-8 text-black">
        {{ $categories->links() }}
    </div>
@endif
@endsection