@extends('layouts.app')

@section('title', 'Sponsor Details - ' . $sponsor->name)
@section('page-title', 'Sponsor Details')
@section('page-description', 'View complete sponsor information')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <a href="{{ route('sponsors.index') }}" class="text-gray-400 hover:text-white transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Sponsors
        </a>
        
        <div class="flex gap-2">
            <a href="{{ route('sponsors.edit', $sponsor) }}" 
               class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Sponsor
            </a>
            <form action="{{ route('sponsors.destroy', $sponsor) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus sponsor ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete Sponsor
                </button>
            </form>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Sponsor Profile Card -->
    <div class="lg:col-span-1">
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6 sticky top-6">
            <!-- Logo -->
            <div class="text-center mb-6">
                @if($sponsor->logo)
                    <img src="{{ Storage::url($sponsor->logo) }}" 
                         class="w-32 h-32 rounded-xl mx-auto object-cover border-2 border-green-500 shadow-xl">
                @else
                    <div class="w-32 h-32 rounded-xl bg-gradient-to-br from-gray-600 to-gray-700 flex items-center justify-center mx-auto shadow-xl">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                @endif
                
                <h2 class="text-2xl font-bold text-white mt-4 mb-2">{{ $sponsor->name }}</h2>
                
                <div class="inline-flex px-3 py-1 rounded-full text-sm font-semibold mb-3 {{ $sponsor->tier_badge }}">
                    {{ ucfirst($sponsor->tier) }}
                </div>
                
                <div class="inline-flex px-3 py-1 rounded-full text-sm ml-2
                    {{ $sponsor->is_active ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300' }}">
                    {{ $sponsor->is_active ? 'Active' : 'Inactive' }}
                </div>
            </div>

            <!-- Contact Information -->
            <div class="border-t border-white/10 pt-4 space-y-3">
                <h3 class="text-sm font-semibold text-gray-300 mb-3">Contact Information</h3>
                
                @if($sponsor->email)
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Email</span>
                    <a href="mailto:{{ $sponsor->email }}" class="text-white text-sm hover:text-green-400">
                        {{ $sponsor->email }}
                    </a>
                </div>
                @endif
                
                @if($sponsor->phone)
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Phone</span>
                    <span class="text-white text-sm">{{ $sponsor->phone }}</span>
                </div>
                @endif
                
                @if($sponsor->website)
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Website</span>
                    <a href="{{ $sponsor->website }}" target="_blank" class="text-green-400 text-sm hover:text-green-300">
                        {{ Str::limit($sponsor->website, 30) }}
                    </a>
                </div>
                @endif
            </div>

            <!-- System Information -->
            <div class="border-t border-white/10 pt-4 mt-4 space-y-3">
                <h3 class="text-sm font-semibold text-gray-300 mb-3">System Information</h3>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Sponsor ID</span>
                    <span class="text-white font-mono text-sm">#{{ $sponsor->id }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Sort Order</span>
                    <span class="text-white text-sm">{{ $sponsor->sort_order }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Created At</span>
                    <span class="text-white text-sm">{{ $sponsor->created_at->format('d M Y, H:i') }}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Last Updated</span>
                    <span class="text-white text-sm">{{ $sponsor->updated_at->format('d M Y, H:i') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sponsor Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Description -->
        @if($sponsor->description)
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/>
                </svg>
                Description
            </h3>
            <div class="text-gray-300 leading-relaxed">
                {{ $sponsor->description }}
            </div>
        </div>
        @endif

        <!-- Associated Events -->
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Sponsored Events
                </h3>
                <span class="text-xs text-gray-400">{{ $sponsor->events->count() }} events</span>
            </div>
            
            @if($sponsor->events->count() > 0)
                <div class="space-y-3">
                    @foreach($sponsor->events as $event)
                        <div class="bg-white/5 rounded-lg p-4 hover:bg-white/10 transition group">
                            <div class="flex justify-between items-start">
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
                                            <h4 class="font-semibold text-white group-hover:text-green-400 transition">
                                                {{ $event->title }}
                                            </h4>
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
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <p class="text-gray-400">No events sponsored yet</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection