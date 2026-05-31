@extends('layouts.app')

@section('title', 'Participant Details - ' . $participant->name)
@section('page-title', 'Participant Details')
@section('page-description', 'View complete participant information, order history, and event history')

@push('styles')
<style>
    .warning-card {
        transition: all 0.3s ease;
    }
    .warning-card:hover {
        transform: translateX(5px);
    }
    .warning-level-1 {
        border-left-color: #f59e0b;
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.05));
    }
    .warning-level-2 {
        border-left-color: #ef4444;
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05));
    }
    .warning-level-3 {
        border-left-color: #6b7280;
        background: linear-gradient(135deg, rgba(107, 114, 128, 0.15), rgba(107, 114, 128, 0.08));
    }
</style>
@endpush

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <a href="{{ route('participants.index') }}" class="text-gray-400 hover:text-white transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Participants
        </a>

        <div class="flex gap-2">
            <a href="{{ route('participants.edit', $participant) }}"
                class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Participant
            </a>
            <form action="{{ route('participants.destroy', $participant) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus participant ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Participant
                </button>
            </form>
            <a href="{{ route('participants.export-pdf', $participant) }}"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export PDF
            </a>
        </div>
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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Participant Profile Card -->
    <div class="lg:col-span-1">
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6 sticky top-6">
            <!-- Avatar -->
            <div class="text-center mb-6">
                @if($participant->photo)
                <img src="{{ Storage::url($participant->photo) }}"
                    class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-green-500 shadow-xl">
                @else
                <div class="w-32 h-32 rounded-full bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center mx-auto shadow-xl">
                    <span class="text-white font-bold text-4xl">
                        {{ strtoupper(substr($participant->name, 0, 2)) }}
                    </span>
                </div>
                @endif

                <h2 class="text-2xl font-bold text-white mt-4 mb-2">{{ $participant->name }}</h2>

                <div class="inline-flex px-3 py-1 rounded-full text-sm font-semibold mb-3
                    @if($participant->status == 'active') bg-green-500/20 text-green-300
                    @else bg-red-500/20 text-red-300
                    @endif">
                    {{ ucfirst($participant->status) }}
                </div>

                <!-- Hash ID -->
                <div class="bg-black/30 rounded-lg p-3 mt-2">
                    <p class="text-gray-400 text-xs mb-1">Hash ID (Login Credential)</p>
                    <code class="text-green-400 font-mono text-sm break-all">{{ $participant->hash_id }}</code>
                    <button onclick="copyHashId()"
                        class="mt-2 text-xs bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 px-2 py-1 rounded transition w-full">
                        Copy Hash ID
                    </button>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="border-t border-white/10 pt-4 space-y-3">
                <h3 class="text-sm font-semibold text-gray-300 mb-3">Personal Information</h3>

                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Email</span>
                    <span class="text-white text-sm">{{ $participant->email }}</span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Phone</span>
                    <span class="text-white text-sm">{{ $participant->phone }}</span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Gender</span>
                    <span class="text-white text-sm capitalize">{{ $participant->gender }}</span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Birthdate</span>
                    <span class="text-white text-sm">{{ $participant->birthdate->format('d M Y') }}</span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Age</span>
                    <span class="text-white text-sm">{{ $participant->birthdate->age }} years</span>
                </div>
            </div>

            <!-- Account Information -->
            <div class="border-t border-white/10 pt-4 mt-4 space-y-3">
                <h3 class="text-sm font-semibold text-gray-300 mb-3">Account Information</h3>

                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Participant ID</span>
                    <span class="text-white font-mono text-sm">#{{ $participant->id }}</span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Registered</span>
                    <span class="text-white text-sm">{{ $participant->created_at->format('d M Y, H:i') }}</span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Last Updated</span>
                    <span class="text-white text-sm">{{ $participant->updated_at->format('d M Y, H:i') }}</span>
                </div>

                @if($participant->last_login_at)
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Last Login</span>
                    <span class="text-white text-sm">{{ $participant->last_login_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Last IP</span>
                    <span class="text-white text-sm">{{ $participant->last_login_ip ?? '-' }}</span>
                </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="border-t border-white/10 pt-4 mt-4">
                <div class="flex gap-2">
                    <a href="{{ route('participants.regenerate-hash', $participant) }}"
                        onclick="return confirm('Yakin ingin mengganti Hash ID? Hash ID lama tidak dapat digunakan lagi.')"
                        class="flex-1 bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg transition text-center text-sm">
                        Regenerate Hash ID
                    </a>
                    <form action="{{ route('participants.toggle-status', $participant) }}" method="POST" class="flex-1">
                        @csrf
                        @method('POST')
                        <button type="submit"
                            class="w-full px-3 py-2 rounded-lg transition text-center text-sm
                                    {{ $participant->status == 'active' ? 'bg-red-500/20 hover:bg-red-500/30 text-red-300' : 'bg-green-500/20 hover:bg-green-500/30 text-green-300' }}">
                            {{ $participant->status == 'active' ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                </div>
                @if($participant->warning_count > 0)
                <div class="mt-2">
                    <a href="{{ route('participants.warnings', $participant) }}" 
                       class="block text-center bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 px-3 py-2 rounded-lg transition text-center text-sm">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        📋 Lihat History Peringatan ({{ $participant->warning_count }})
                    </a>
                </div>
                @endif
            </div>

            <!-- Notes -->
            @if($participant->notes)
            <div class="border-t border-white/10 pt-4 mt-4">
                <h3 class="text-sm font-semibold text-gray-300 mb-2">Notes</h3>
                <p class="text-gray-400 text-sm bg-black/20 rounded-lg p-3">{{ $participant->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Orders & Activity -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white/5 rounded-xl border border-white/10 p-4 text-center">
                <p class="text-2xl font-bold text-green-400">{{ $totalOrders }}</p>
                <p class="text-xs text-gray-400">Total Orders</p>
            </div>
            <div class="bg-white/5 rounded-xl border border-white/10 p-4 text-center">
                <p class="text-2xl font-bold text-blue-400">{{ $paidOrders }}</p>
                <p class="text-xs text-gray-400">Paid Orders</p>
            </div>
            <div class="bg-white/5 rounded-xl border border-white/10 p-4 text-center">
                <p class="text-2xl font-bold text-purple-400">
                    Rp {{ number_format($totalSpent, 0, ',', '.') }}
                </p>
                <p class="text-xs text-gray-400">Total Spent</p>
            </div>
            <div class="bg-white/5 rounded-xl border border-white/10 p-4 text-center">
                <p class="text-2xl font-bold text-orange-400">{{ $totalEventsJoined }}</p>
                <p class="text-xs text-gray-400">Events Joined</p>
            </div>
        </div>

        <!-- WARNING SYSTEM SECTION -->
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-4">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Status Peringatan
                    </h3>
                    
                    @if($participant->warning_count > 0)
                    <a href="{{ route('participants.warnings', $participant) }}" 
                       class="text-blue-400 hover:text-blue-300 text-sm transition flex items-center gap-1 bg-blue-500/10 px-3 py-1 rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Lihat Semua Peringatan ({{ $participant->warning_count }})
                    </a>
                    @endif
                </div>
                
                @if(auth()->user() && auth()->user()->role === 'admin')
                <button type="button" 
                        onclick="openWarningModal()"
                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-sm transition flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Beri Peringatan
                </button>
                @endif
            </div>
            
            @if($participant->current_warning_level == 0 && !$participant->is_suspended)
                <div class="text-center py-6">
                    <svg class="w-16 h-16 mx-auto text-green-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-gray-400">Tidak ada peringatan</p>
                    <p class="text-xs text-green-400 mt-1">Status: Aktif & Aman</p>
                </div>
            @else
                <!-- Current Warning Level Card -->
                <div class="rounded-lg p-4 mb-4 warning-card warning-level-{{ min($participant->current_warning_level, 3) }} border-l-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-lg font-bold text-white">
                                    Warning Level {{ $participant->current_warning_level }}
                                </span>
                                @if($participant->is_suspended)
                                    <span class="px-2 py-0.5 bg-red-500/30 text-red-300 rounded-full text-xs">SUSPENDED</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-300">
                                {!! $participant->current_warning_level == 1 ? '⚠️ Tidak bisa join <strong class="text-yellow-300">2 event berikutnya</strong>' : 
                                   ($participant->current_warning_level == 2 ? '⚠️ Tidak bisa join <strong class="text-red-300">5 event berikutnya</strong>' : 
                                   '⛔ Akun <strong class="text-gray-300">dinonaktifkan permanen</strong>') !!}
                            </p>
                            @if($participant->suspended_until && $participant->is_suspended)
                                <p class="text-xs text-gray-400 mt-2">
                                    Suspended until: {{ $participant->suspended_until->format('d M Y H:i') }}
                                    @php
                                        $remainingDays = now()->diffInDays($participant->suspended_until, false);
                                    @endphp
                                    @if($remainingDays > 0)
                                        <span class="text-yellow-400">({{ $remainingDays }} days remaining)</span>
                                    @endif
                                </p>
                            @endif
                            @if($participant->suspension_reason)
                                <p class="text-xs text-gray-500 mt-1">Reason: {{ $participant->suspension_reason }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Active Warnings List -->
                @if($participant->activeWarnings && $participant->activeWarnings->count() > 0)
                    <div class="mt-4">
                        <label class="text-xs text-gray-400 mb-2 block">Riwayat Peringatan Aktif:</label>
                        <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                            @foreach($participant->activeWarnings as $warning)
                            <div class="bg-white/5 rounded-lg p-3 warning-card">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-sm font-semibold text-yellow-400">
                                                Warning {{ $warning->warning_level }}
                                            </span>
                                            <span class="text-xs text-gray-500">
                                                {{ $warning->issued_at->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-300">
                                            <strong class="text-gray-400">Reason:</strong> {{ $warning->reason }}
                                        </p>
                                        @if($warning->description)
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $warning->description }}
                                            </p>
                                        @endif
                                        <p class="text-xs text-gray-500 mt-1">
                                            Issued by: {{ $warning->issuer->name ?? 'System' }}
                                        </p>
                                    </div>
                                    @if(auth()->user() && auth()->user()->role === 'admin')
                                    <form action="{{ route('participants.remove-warning', [$participant->id, $warning->id]) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Yakin ingin menghapus warning ini?')"
                                          class="ml-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-300 transition" title="Hapus Warning">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>

        <!-- Event History -->
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Event History
                </h3>
                <div class="flex gap-2">
                    @if($upcomingEvents->count() > 0)
                    <span class="text-xs px-2 py-1 bg-yellow-500/20 text-yellow-300 rounded-full">{{ $upcomingEvents->count() }} Upcoming</span>
                    @endif
                    @if($ongoingEvents->count() > 0)
                    <span class="text-xs px-2 py-1 bg-green-500/20 text-green-300 rounded-full">{{ $ongoingEvents->count() }} Ongoing</span>
                    @endif
                    @if($pastEvents->count() > 0)
                    <span class="text-xs px-2 py-1 bg-gray-500/20 text-gray-300 rounded-full">{{ $pastEvents->count() }} Finished</span>
                    @endif
                </div>
            </div>

            @if($eventsJoined->count() > 0)
            <div class="space-y-3">
                @foreach($eventsJoined as $event)
                <div class="bg-white/5 rounded-lg p-4 hover:bg-white/10 transition group">
                    <div class="flex flex-wrap justify-between items-start gap-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2 flex-wrap">
                                @if($event->image)
                                <img src="{{ Storage::url($event->image) }}" class="w-10 h-10 rounded-lg object-cover">
                                @else
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-gray-600 to-gray-700 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                @endif
                                <div>
                                    <h4 class="font-semibold text-white group-hover:text-green-400 transition">
                                        {{ $event->title }}
                                    </h4>
                                    <div class="flex flex-wrap gap-3 text-xs text-gray-400">
                                        <span>{{ $event->start_date->format('d M Y') }} - {{ $event->end_date->format('d M Y') }}</span>
                                        <span>{{ $event->location }}</span>
                                        <span class="text-green-400">{{ $event->category->name ?? 'Uncategorized' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            @if($event->status == 'upcoming')
                            <span class="px-2 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-xs">Upcoming</span>
                            @elseif($event->status == 'ongoing')
                            <span class="px-2 py-1 bg-green-500/20 text-green-300 rounded-full text-xs">Ongoing</span>
                            @else
                            <span class="px-2 py-1 bg-gray-500/20 text-gray-300 rounded-full text-xs">Finished</span>
                            @endif
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-white/10 flex justify-end opacity-0 group-hover:opacity-100 transition">
                        <a href="{{ route('events.show', $event) }}" class="text-blue-400 hover:text-blue-300 text-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View Event Details
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <p class="text-gray-400">No events joined yet</p>
                <p class="text-sm text-gray-500 mt-1">This participant hasn't registered for any events</p>
            </div>
            @endif
        </div>

        <!-- Order History -->
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Order History
                </h3>
                <span class="text-xs text-gray-400">{{ $totalOrders }} total orders</span>
            </div>

            @if($participant->orders()->count() > 0)
            <div class="space-y-3">
                @foreach($participant->orders()->latest()->take(10)->get() as $order)
                <div class="bg-white/5 rounded-lg p-4 hover:bg-white/10 transition group">
                    <div class="flex flex-wrap justify-between items-start gap-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2 flex-wrap">
                                <span class="font-mono text-green-400 text-sm">{{ $order->invoice_number }}</span>
                                <span class="text-xs text-gray-500">|</span>
                                <span class="text-white font-semibold">{{ $order->event->title ?? 'N/A' }}</span>
                            </div>
                            <div class="flex flex-wrap gap-3 text-xs text-gray-400">
                                <span>Ticket: {{ $order->ticket_code }}</span>
                                <span>Order Date: {{ $order->created_at->format('d M Y') }}</span>
                                @if($order->event)
                                <span>Event Date: {{ $order->event->start_date->format('d M Y') }}</span>
                                @endif
                            </div>
                            @if($order->payment && $order->payment->payment_method)
                            <div class="text-xs text-gray-500 mt-1">
                                Payment: {{ $order->payment->payment_method }}
                            </div>
                            @endif
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-green-400">
                                @if($order->total_price > 0)
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                @else
                                <span class="text-blue-400">FREE</span>
                                @endif
                            </div>
                            <div class="mt-1">
                                @if($order->status == 'pending')
                                <span class="px-2 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-xs">Pending</span>
                                @elseif($order->status == 'paid')
                                <span class="px-2 py-1 bg-green-500/20 text-green-300 rounded-full text-xs">Paid</span>
                                @elseif($order->status == 'free')
                                <span class="px-2 py-1 bg-blue-500/20 text-blue-300 rounded-full text-xs">Free</span>
                                @else
                                <span class="px-2 py-1 bg-red-500/20 text-red-300 rounded-full text-xs">Cancelled</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-white/10 flex justify-end opacity-0 group-hover:opacity-100 transition">
                        <a href="{{ route('orders.show', $order) }}" class="text-blue-400 hover:text-blue-300 text-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View Order Details
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            @if($totalOrders > 10)
            <div class="mt-4 text-center">
                <a href="{{ route('orders.index') }}?participant={{ $participant->id }}"
                    class="text-green-400 hover:text-green-300 text-sm inline-flex items-center gap-1">
                    View all {{ $totalOrders }} orders
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            @endif
            @else
            <div class="text-center py-8">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <p class="text-gray-400">No orders yet</p>
                <p class="text-sm text-gray-500 mt-1">This participant hasn't made any orders</p>
            </div>
            @endif
        </div>

        <!-- Recent Activity -->
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Recent Activity
            </h3>

            <div class="space-y-3">
                <!-- Registration Activity -->
                <div class="flex items-center gap-3 text-sm">
                    <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-gray-300">Registered as participant</p>
                        <p class="text-xs text-gray-500">{{ $participant->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                <!-- Last Login Activity -->
                @if($participant->last_login_at)
                <div class="flex items-center gap-3 text-sm">
                    <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-gray-300">Last login</p>
                        <p class="text-xs text-gray-500">{{ $participant->last_login_at->format('d M Y, H:i') }} from {{ $participant->last_login_ip ?? 'Unknown IP' }}</p>
                    </div>
                </div>
                @endif

                <!-- Most Recent Order -->
                @php $recentOrder = $participant->orders()->latest()->first(); @endphp
                @if($recentOrder)
                <div class="flex items-center gap-3 text-sm">
                    <div class="w-8 h-8 rounded-full bg-purple-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-gray-300">Most recent order</p>
                        <p class="text-xs text-gray-500">{{ $recentOrder->created_at->format('d M Y, H:i') }} - {{ $recentOrder->invoice_number }}</p>
                    </div>
                </div>
                @endif

                <!-- Most Recent Event Joined -->
                @php $recentEvent = $eventsJoined->first(); @endphp
                @if($recentEvent)
                <div class="flex items-center gap-3 text-sm">
                    <div class="w-8 h-8 rounded-full bg-orange-500/20 flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-gray-300">Most recent event joined</p>
                        <p class="text-xs text-gray-500">{{ $recentEvent->title }} - {{ $recentEvent->start_date->format('d M Y') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal for issuing warning -->
<div id="warningModal" class="fixed inset-0 bg-black/70 z-50 hidden items-center justify-center p-4" onclick="closeWarningModal()">
    <div class="bg-gray-900 rounded-xl max-w-md w-full border border-white/10" onclick="event.stopPropagation()">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Beri Peringatan
                </h3>
                <button onclick="closeWarningModal()" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('participants.issue-warning', $participant->id) }}" method="POST">
                @csrf
                
                <!-- Pilih Level Warning dengan Dropdown -->
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 font-semibold">Level Peringatan *</label>
                    <select name="warning_level" id="warningLevelSelect" 
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-red-500"
                            required>
                        <option value="" class="text-black">-- Pilih Level Peringatan --</option>
                        <option value="1" class="text-black" data-sanksi="Tidak bisa join 2 event berikutnya">
                            ⚠️ Warning Level 1 - Tidak bisa join 2 event berikutnya
                        </option>
                        <option value="2" class="text-black" data-sanksi="Tidak bisa join 5 event berikutnya">
                            ⚠️ Warning Level 2 - Tidak bisa join 5 event berikutnya
                        </option>
                        <option value="3" class="text-black" data-sanksi="Akun dinonaktifkan permanen">
                            ⛔ Warning Level 3 - Suspensi permanen
                        </option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1" id="warningLevelHint">
                        Pilih level peringatan yang akan diberikan
                    </p>
                </div>
                
                <!-- Alasan Peringatan -->
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 font-semibold">Alasan Peringatan *</label>
                    <select name="reason" id="reasonSelect" 
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-red-500"
                            required>
                        <option value="" class="text-black">-- Pilih Alasan --</option>
                        <option value="Melanggar peraturan event" class="text-black">Melanggar peraturan event</option>
                        <option value="Perilaku tidak sopan" class="text-black">Perilaku tidak sopan</option>
                        <option value="Membatalkan partisipasi tanpa pemberitahuan" class="text-black">Membatalkan partisipasi tanpa pemberitahuan</option>
                        <option value="Melanggar kode etik" class="text-black">Melanggar kode etik</option>
                        <option value="Menyebabkan gangguan selama event" class="text-black">Menyebabkan gangguan selama event</option>
                        <option value="Pelanggaran berulang" class="text-black">Pelanggaran berulang</option>
                        <option value="Lainnya" class="text-black">Lainnya (isi manual)</option>
                    </select>
                </div>
                
                <!-- Deskripsi Manual (muncul jika pilih "Lainnya") -->
                <div class="mb-4 hidden" id="manualReasonContainer">
                    <label class="block text-gray-300 mb-2 font-semibold">Deskripsi Alasan *</label>
                    <input type="text" 
                           name="manual_reason" 
                           id="manualReason"
                           class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-red-500"
                           placeholder="Tulis alasan peringatan...">
                </div>
                
                <!-- Deskripsi Detail -->
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 font-semibold">Deskripsi Detail (Opsional)</label>
                    <textarea name="description" 
                              id="warningDescription"
                              rows="3"
                              class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-red-500"
                              placeholder="Jelaskan detail pelanggaran yang terjadi..."></textarea>
                </div>
                
                <!-- Preview Sanksi -->
                <div id="sanctionPreview" class="hidden bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-3 mb-4">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-yellow-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-yellow-300 font-semibold mb-1">⚠️ Konsekuensi yang akan diterima:</p>
                            <p id="sanctionText" class="text-sm text-white"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Info Current Warning Level -->
                <div class="bg-white/5 rounded-lg p-3 mb-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-400">Current Warning Level:</span>
                        <span class="font-semibold 
                            @if($participant->current_warning_level == 0) text-green-400
                            @elseif($participant->current_warning_level == 1) text-yellow-400
                            @elseif($participant->current_warning_level == 2) text-orange-400
                            @else text-red-400 @endif">
                            {{ $participant->current_warning_level == 0 ? 'Clean' : 'Level ' . $participant->current_warning_level }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-sm mt-1">
                        <span class="text-gray-400">After this warning:</span>
                        <span class="font-semibold text-red-400" id="nextWarningLevel">
                            Level {{ $participant->current_warning_level + 1 }}
                        </span>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                        Beri Peringatan
                    </button>
                    <button type="button" onclick="closeWarningModal()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyHashId() {
        const hashId = '{{ $participant->hash_id }}';
        navigator.clipboard.writeText(hashId).then(() => {
            const btn = event.target;
            const originalText = btn.innerText;
            btn.innerText = 'Copied!';
            setTimeout(() => {
                btn.innerText = originalText;
            }, 2000);
        });
    }
    
    function openWarningModal() {
        const modal = document.getElementById('warningModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    
    function closeWarningModal() {
        const modal = document.getElementById('warningModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }
    
    // Handle warning level selection
    const warningLevelSelect = document.getElementById('warningLevelSelect');
    const sanctionPreview = document.getElementById('sanctionPreview');
    const sanctionText = document.getElementById('sanctionText');
    const nextWarningLevel = document.getElementById('nextWarningLevel');
    const currentLevel = {{ $participant->current_warning_level }};
    
    if (warningLevelSelect) {
        warningLevelSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const sanction = selectedOption.getAttribute('data-sanksi');
            const level = this.value;
            
            if (level) {
                if (sanctionText) sanctionText.textContent = sanction;
                if (sanctionPreview) sanctionPreview.classList.remove('hidden');
                
                const newLevel = parseInt(level);
                if (nextWarningLevel) {
                    nextWarningLevel.textContent = 'Level ' + newLevel;
                    nextWarningLevel.className = newLevel >= 3 ? 'font-semibold text-red-400' : 'font-semibold text-yellow-400';
                }
            } else {
                if (sanctionPreview) sanctionPreview.classList.add('hidden');
                if (nextWarningLevel) nextWarningLevel.textContent = 'Level ' + (currentLevel + 1);
            }
        });
    }
    
    // Handle reason selection
    const reasonSelect = document.getElementById('reasonSelect');
    const manualReasonContainer = document.getElementById('manualReasonContainer');
    const manualReason = document.getElementById('manualReason');
    
    if (reasonSelect) {
        reasonSelect.addEventListener('change', function() {
            if (this.value === 'Lainnya') {
                if (manualReasonContainer) manualReasonContainer.classList.remove('hidden');
                if (manualReason) manualReason.required = true;
            } else {
                if (manualReasonContainer) manualReasonContainer.classList.add('hidden');
                if (manualReason) {
                    manualReason.required = false;
                    manualReason.value = '';
                }
            }
        });
    }
    
    // Handle form submission to combine reason
    const warningForm = document.querySelector('#warningModal form');
    if (warningForm) {
        warningForm.addEventListener('submit', function(e) {
            const warningLevel = warningLevelSelect ? warningLevelSelect.value : null;
            let reason = reasonSelect ? reasonSelect.value : null;
            
            if (!warningLevel) {
                e.preventDefault();
                alert('Pilih level peringatan terlebih dahulu!');
                return false;
            }
            
            if (!reason) {
                e.preventDefault();
                alert('Pilih alasan peringatan!');
                return false;
            }
            
            if (reason === 'Lainnya') {
                const manualReasonValue = manualReason ? manualReason.value.trim() : '';
                if (!manualReasonValue) {
                    e.preventDefault();
                    alert('Isi deskripsi alasan untuk pilihan "Lainnya"!');
                    return false;
                }
                reason = manualReasonValue;
            }
            
            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'reason';
            reasonInput.value = `[Level ${warningLevel}] ${reason}`;
            this.appendChild(reasonInput);
            
            if (reasonSelect) reasonSelect.disabled = true;
            
            return true;
        });
    }
    
    // Close modal with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeWarningModal();
        }
    });
</script>
@endpush
@endsection