@extends('layouts.app')

@section('title', 'Organizer Dashboard')
@section('page-title', 'Organizer Dashboard')
@section('page-description', 'Overview of your events and participants')

@section('content')
<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-br from-blue-500/10 to-blue-600/10 rounded-xl border border-blue-500/30 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm">My Events</p>
                <p class="text-3xl font-bold text-blue-400">{{ number_format($totalEvents) }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-green-500/10 to-green-600/10 rounded-xl border border-green-500/30 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm">Total Orders</p>
                <p class="text-3xl font-bold text-green-400">{{ number_format($totalOrders) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-purple-500/10 to-purple-600/10 rounded-xl border border-purple-500/30 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm">Total Participants</p>
                <p class="text-3xl font-bold text-purple-400">{{ number_format($totalParticipants) }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-yellow-500/10 to-yellow-600/10 rounded-xl border border-yellow-500/30 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm">Total Revenue</p>
                <p class="text-3xl font-bold text-yellow-400">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Upcoming Events -->
    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                My Upcoming Events
            </h3>
            <a href="{{ route('events.create') }}" class="text-green-400 hover:text-green-300 text-sm">+ Buat Event</a>
        </div>
        
        @forelse($upcomingEvents as $event)
        <div class="bg-white/5 rounded-lg p-3 mb-2 hover:bg-white/10 transition">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <p class="font-semibold text-white">{{ $event->title }}</p>
                    <p class="text-xs text-gray-400">{{ $event->date->format('d M Y, H:i') }} • {{ $event->location }}</p>
                    <div class="mt-1">
                        <div class="flex justify-between text-xs text-gray-400 mb-1">
                            <span>Kuota: {{ $event->registered }}/{{ $event->quota }}</span>
                            <span>{{ $event->percentage }}%</span>
                        </div>
                        <div class="w-full bg-white/10 rounded-full h-1.5">
                            <div class="bg-green-500 h-1.5 rounded-full" style="width: {{ $event->percentage }}%"></div>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-xs">Upcoming</span>
                    <a href="{{ route('events.edit', $event->id) }}" class="text-blue-400 hover:text-blue-300 text-xs block mt-1">Edit</a>
                </div>
            </div>
        </div>
        @empty
        <p class="text-gray-400 text-center py-4">Anda belum memiliki event yang akan datang</p>
        @endforelse
    </div>
    
    <!-- Ongoing Events -->
    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                </svg>
                Ongoing Events
            </h3>
        </div>
        
        @forelse($ongoingEvents as $event)
        <div class="bg-white/5 rounded-lg p-3 mb-2 hover:bg-white/10 transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="font-semibold text-white">{{ $event->title }}</p>
                    <p class="text-xs text-gray-400">Sisa {{ $event->remaining_days }} hari • {{ $event->location }}</p>
                    <p class="text-xs text-green-400 mt-1">{{ number_format($event->registered) }} peserta</p>
                </div>
                <span class="px-2 py-1 bg-green-500/20 text-green-300 rounded-full text-xs">Ongoing</span>
            </div>
        </div>
        @empty
        <p class="text-gray-400 text-center py-4">Tidak ada event yang sedang berlangsung</p>
        @endforelse
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- History Events -->
    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                History Events (Selesai)
            </h3>
        </div>
        
        @forelse($historyEvents as $event)
        <div class="bg-white/5 rounded-lg p-3 mb-2 hover:bg-white/10 transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="font-semibold text-white">{{ $event->title }}</p>
                    <p class="text-xs text-gray-400">{{ $event->date->format('d M Y') }} • {{ $event->location }}</p>
                    <div class="flex gap-3 mt-1 text-xs">
                        <span class="text-green-400">{{ number_format($event->registered) }} peserta</span>
                        <span class="text-yellow-400">Rp {{ number_format($event->revenue, 0, ',', '.') }}</span>
                    </div>
                </div>
                <span class="px-2 py-1 bg-gray-500/20 text-gray-300 rounded-full text-xs">Selesai</span>
            </div>
        </div>
        @empty
        <p class="text-gray-400 text-center py-4">Belum ada event yang selesai</p>
        @endforelse
    </div>
    
    <!-- Recent Participants for My Events -->
    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Recent Participants
            </h3>
        </div>
        
        @forelse($recentParticipants as $participant)
        <div class="bg-white/5 rounded-lg p-3 mb-2 hover:bg-white/10 transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="font-semibold text-white">{{ $participant->name }}</p>
                    <p class="text-xs text-gray-400">{{ $participant->email }}</p>
                    <p class="text-xs text-green-400 mt-1">Event: {{ $participant->last_event }}</p>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 bg-green-500/20 text-green-300 rounded-full text-xs">{{ $participant->total_orders }} orders</span>
                </div>
            </div>
        </div>
        @empty
        <p class="text-gray-400 text-center py-4">Belum ada participant</p>
        @endforelse
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Top My Events -->
    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            Top Events (Most Registered)
        </h3>
        
        @foreach($topEvents as $index => $event)
        <div class="flex justify-between items-center py-2 border-b border-white/10">
            <div class="flex items-center gap-3">
                <span class="text-2xl font-bold text-gray-500 w-8">#{{ $index + 1 }}</span>
                <div>
                    <p class="text-white">{{ $event->title }}</p>
                    <p class="text-xs text-gray-400">Quota: {{ number_format($event->quota) }}</p>
                </div>
            </div>
            <span class="text-green-400 font-semibold">{{ number_format($event->registered) }} peserta</span>
        </div>
        @endforeach
    </div>
    
    <!-- Recent Orders for My Events -->
    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            Recent Orders
        </h3>
        
        @foreach($recentOrders as $order)
        <div class="flex justify-between items-center py-2 border-b border-white/10">
            <div>
                <p class="text-white text-sm">{{ $order->participant_name }}</p>
                <p class="text-xs text-gray-400">{{ $order->event_title }}</p>
            </div>
            <div class="text-right">
                <p class="text-green-400 font-semibold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                <span class="px-2 py-1 text-xs rounded-full 
                    @if($order->status == 'pending') bg-yellow-500/20 text-yellow-300
                    @elseif($order->status == 'paid') bg-green-500/20 text-green-300
                    @else bg-red-500/20 text-red-300 @endif">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection