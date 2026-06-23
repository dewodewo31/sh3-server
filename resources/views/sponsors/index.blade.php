@extends('layouts.app')

@section('title', 'Sponsor Management')
@section('page-title', 'Sponsors')
@section('page-description', 'Manage all event sponsors')

@section('stats')
<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
        </svg>
        <p class="text-gray-400 text-sm">Total Sponsors</p>
    </div>
    <h3 class="text-2xl font-bold text-green-400">{{ $totalSponsors }}</h3>
</div>

<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-gray-400 text-sm">Active Sponsors</p>
    </div>
    <h3 class="text-2xl font-bold text-green-400">{{ $activeSponsors }}</h3>
</div>

<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
        </svg>
        <p class="text-gray-400 text-sm">Sponsor Tiers</p>
    </div>
    <h3 class="text-2xl font-bold text-purple-400">5</h3>
</div>
@endsection

@section('content')
<div class="flex flex-wrap justify-between items-center gap-4 mb-6">
    <h2 class="text-2xl font-bold text-white">Sponsor Management</h2>
    <div class="flex gap-3">
        <a href="{{ route('sponsors.create') }}" 
           class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Sponsor
        </a>
    </div>
</div>

@if(session('success'))
<div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-4">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-4">
    {{ session('error') }}
</div>
@endif

<!-- FILTERS -->
<div class="mb-6">
    <form method="GET" action="{{ route('sponsors.index') }}" class="flex flex-wrap gap-3">
        <select name="year" class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white">
            <option value="">All Years</option>
            @foreach($availableYears as $yr)
                <option value="{{ $yr }}" {{ request('year') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
            @endforeach
        </select>

        <select name="tier" class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white">
            <option value="">All Tiers</option>
            <option value="platinum" {{ request('tier') == 'platinum' ? 'selected' : '' }}>Platinum</option>
            <option value="gold" {{ request('tier') == 'gold' ? 'selected' : '' }}>Gold</option>
            <option value="silver" {{ request('tier') == 'silver' ? 'selected' : '' }}>Silver</option>
            <option value="bronze" {{ request('tier') == 'bronze' ? 'selected' : '' }}>Bronze</option>
            <option value="partner" {{ request('tier') == 'partner' ? 'selected' : '' }}>Partner</option>
        </select>
        
        <input type="text" name="search" value="{{ request('search') }}" 
               placeholder="Search sponsor..." 
               class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white w-64">
        
        <button type="submit" class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded-lg transition">
            Filter
        </button>
        
        @if(request()->hasAny(['year', 'tier', 'search']))
        <a href="{{ route('sponsors.index') }}" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg transition">
            Reset
        </a>
        @endif
    </form>
</div>

<!-- STATS — Tambah Year Stats -->
<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <p class="text-gray-400 text-sm">Current Year</p>
    </div>
    <h3 class="text-2xl font-bold text-blue-400">{{ $currentYearSponsors }}</h3>
</div>

<!-- SPONSORS GRID -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($sponsors as $sponsor)
    
    <!-- ✅ CARD STARTS HERE — INSIDE the loop -->
    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 overflow-hidden hover:border-green-500/50 transition-all duration-300 group">
        
        <!-- ✅ SPONSOR HEADER — Now INSIDE the loop, $sponsor exists -->
        <div class="p-4 border-b border-white/10 bg-white/5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @if($sponsor->logo)
                        <img src="{{ Storage::url($sponsor->logo) }}" class="w-12 h-12 rounded-lg object-cover">
                    @else
                        <div class="w-12 h-12 bg-gradient-to-br from-gray-600 to-gray-700 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    @endif
                    <div>
                        <h3 class="font-semibold text-white group-hover:text-green-400 transition">
                            {{ $sponsor->name }}
                        </h3>
                        <div class="flex gap-2 mt-1">
                            <span class="px-2 py-0.5 rounded-full text-xs {{ $sponsor->tier_badge }}">
                                {{ ucfirst($sponsor->tier) }}
                            </span>
                            @if($sponsor->year)
                            <span class="px-2 py-0.5 rounded-full text-xs bg-blue-500/20 text-blue-300">
                                {{ $sponsor->year }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex gap-1">
                    @if($sponsor->is_active)
                        <span class="px-2 py-1 bg-green-500/20 text-green-300 rounded-full text-xs">Active</span>
                    @else
                        <span class="px-2 py-1 bg-red-500/20 text-red-300 rounded-full text-xs">Inactive</span>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Sponsor Body -->
        <div class="p-4">
            <div class="space-y-2 text-sm">
                @if($sponsor->website)
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.66 0 3-4.03 3-9s-1.34-9-3-9m0 18c-1.66 0-3-4.03-3-9s1.34-9 3-9"/>
                    </svg>
                    <a href="{{ $sponsor->website }}" target="_blank" class="text-gray-300 hover:text-green-400 truncate">
                        {{ Str::limit($sponsor->website, 30) }}
                    </a>
                </div>
                @endif
                
                @if($sponsor->email)
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-gray-300">{{ $sponsor->email }}</span>
                </div>
                @endif
                
                @if($sponsor->phone)
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <span class="text-gray-300">{{ $sponsor->phone }}</span>
                </div>
                @endif
                
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span class="text-gray-300">{{ $sponsor->events->count() }} Events</span>
                </div>
            </div>
            
            @if($sponsor->description)
            <p class="text-xs text-gray-400 mt-3 line-clamp-2">{{ Str::limit($sponsor->description, 80) }}</p>
            @endif
        </div>
        
        <!-- Sponsor Footer -->
        <div class="p-4 border-t border-white/10 bg-white/5">
            <div class="flex justify-between items-center">
                <div class="flex gap-2">
                    <a href="{{ route('sponsors.show', $sponsor) }}" class="text-blue-400 hover:text-blue-300 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                    <a href="{{ route('sponsors.edit', $sponsor) }}" class="text-yellow-400 hover:text-yellow-300 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                    <form action="{{ route('sponsors.toggle-status', $sponsor) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-blue-400 hover:text-blue-300 transition">
                            @if($sponsor->is_active)
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            @endif
                        </button>
                    </form>
                </div>
                <div class="text-gray-500 text-xs">#{{ $sponsor->id }}</div>
            </div>
        </div>
    </div>
    <!-- ✅ CARD ENDS HERE -->
    
    @empty
    <div class="col-span-full">
        <div class="text-center py-12">
            <svg class="w-24 h-24 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <p class="text-gray-400 text-lg mb-4">Belum ada sponsor</p>
            <a href="{{ route('sponsors.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Sponsor Pertama
            </a>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($sponsors->hasPages())
<div class="mt-6">
    {{ $sponsors->links() }}
</div>
@endif
@endsection