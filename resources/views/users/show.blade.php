@extends('layouts.app')

@section('title', 'User Details - ' . $user->name)
@section('page-title', 'User Details')
@section('page-description', 'View complete user information')

@section('content')
<div class="mb-6">
    <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-white transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Users
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- User Profile Card -->
    <div class="lg:col-span-1">
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6 sticky top-6">
            <div class="text-center mb-6">
                <div class="w-32 h-32 rounded-full bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center mx-auto mb-4 shadow-xl">
                    <span class="text-white font-bold text-4xl">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </span>
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">{{ $user->name }}</h2>
                <div class="inline-flex px-3 py-1 rounded-full text-sm font-semibold mb-3
                    @if($user->role == 'admin') bg-purple-500/20 text-purple-300
                    @elseif($user->role == 'organizer') bg-yellow-500/20 text-yellow-300
                    @else bg-blue-500/20 text-blue-300
                    @endif">
                    {{ ucfirst($user->role) }}
                </div>
                <p class="text-gray-400">{{ $user->email }}</p>
            </div>

            <div class="border-t border-white/10 pt-4 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">User ID</span>
                    <span class="text-white font-mono">#{{ $user->id }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Joined</span>
                    <span class="text-white">{{ $user->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Last Updated</span>
                    <span class="text-white">{{ $user->updated_at->format('d M Y, H:i') }}</span>
                </div>
            </div>

            <div class="flex gap-3 mt-6 pt-4 border-t border-white/10">
                <a href="{{ route('users.edit', $user) }}" class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition text-center">
                    Edit User
                </a>
                @if($user->id !== auth()->id())
                <form action="{{ route('users.destroy', $user) }}" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            onclick="return confirm('Yakin ingin menghapus user {{ $user->name }}?')"
                            class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                        Delete
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <!-- User Activity -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white/5 rounded-xl border border-white/10 p-4 text-center">
                <p class="text-2xl font-bold text-green-400">{{ $user->orders->count() }}</p>
                <p class="text-xs text-gray-400">Total Orders</p>
            </div>
            <div class="bg-white/5 rounded-xl border border-white/10 p-4 text-center">
                <p class="text-2xl font-bold text-yellow-400">{{ $user->eventsCreated->count() }}</p>
                <p class="text-xs text-gray-400">Events Created</p>
            </div>
            <div class="bg-white/5 rounded-xl border border-white/10 p-4 text-center">
                <p class="text-2xl font-bold text-blue-400">{{ $user->uploadedGalleries->count() }}</p>
                <p class="text-xs text-gray-400">Galleries Uploaded</p>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Recent Orders
            </h3>
            
            @if($user->orders->count() > 0)
                <div class="space-y-2">
                    @foreach($user->orders->take(5) as $order)
                        <div class="bg-white/5 rounded-lg p-3 flex justify-between items-center">
                            <div>
                                <p class="font-semibold">{{ $order->invoice_number }}</p>
                                <p class="text-xs text-gray-400">{{ $order->event->title ?? 'N/A' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-green-400">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-400">{{ ucfirst($order->status) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-center py-4">No orders yet</p>
            @endif
        </div>

        <!-- Events Created -->
        @if($user->role == 'organizer')
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Events Created
            </h3>
            
            @if($user->eventsCreated->count() > 0)
                <div class="space-y-2">
                    @foreach($user->eventsCreated->take(5) as $event)
                        <div class="bg-white/5 rounded-lg p-3 flex justify-between items-center">
                            <div>
                                <p class="font-semibold">{{ $event->title }}</p>
                                <p class="text-xs text-gray-400">{{ $event->start_date->format('d M Y') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-xs px-2 py-1 rounded-full
                                    @if($event->status == 'upcoming') bg-yellow-500/20 text-yellow-300
                                    @elseif($event->status == 'ongoing') bg-green-500/20 text-green-300
                                    @else bg-gray-500/20 text-gray-300
                                    @endif">
                                    {{ ucfirst($event->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-center py-4">No events created</p>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection