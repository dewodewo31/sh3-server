@extends('layouts.app')

@section('title', 'Participant Warnings - ' . ($participant->name ?? 'Details'))
@section('page-title', 'Warning History')
@section('page-description', 'View all warnings for participant')

@section('content')
<div class="mb-6">
    <a href="{{ route('participants.show', $participant->id) }}" class="text-gray-400 hover:text-white transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Participant Details
    </a>
</div>

<div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-white">Warning History</h2>
            <p class="text-sm text-gray-400">
                Participant: <span class="text-green-400">{{ $participant->name ?? 'N/A' }}</span> 
                (Hash ID: <code class="text-purple-400">{{ $participant->hash_id ?? 'N/A' }}</code>)
            </p>
        </div>
        <div class="text-right">
            <div class="text-sm text-gray-400">Current Level</div>
            <div class="text-2xl font-bold 
                @if($warnings['current_level'] == 0) text-green-400
                @elseif($warnings['current_level'] == 1) text-yellow-400
                @elseif($warnings['current_level'] == 2) text-orange-400
                @else text-red-400 @endif">
                {{ $warnings['current_level'] == 0 ? 'Clean' : 'Level ' . $warnings['current_level'] }}
            </div>
            @if($warnings['warning_count'] > 0)
                <div class="text-xs text-gray-500">Total Warnings: {{ $warnings['warning_count'] }}</div>
            @endif
        </div>
    </div>

    @if($warnings['suspension'] && $warnings['suspension']['is_suspended'])
        <div class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <p class="font-semibold text-red-300">Account Suspended</p>
                    <p class="text-sm text-gray-300">
                        @if($warnings['suspension']['type'] == 'temporary')
                            Suspended until: {{ \Carbon\Carbon::parse($warnings['suspension']['until'])->format('d M Y H:i') }}
                            ({{ $warnings['suspension']['remaining_days'] }} days remaining)
                        @else
                            Permanently suspended
                        @endif
                    </p>
                    @if($warnings['suspension']['reason'])
                        <p class="text-xs text-gray-400 mt-1">Reason: {{ $warnings['suspension']['reason'] }}</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @if($warnings['warnings'] && $warnings['warnings']->count() > 0)
        <div class="space-y-3">
            @foreach($warnings['warnings'] as $warning)
            <div class="bg-white/5 rounded-lg p-4 border-l-4 
                @if($warning->warning_level == 1) border-yellow-500
                @elseif($warning->warning_level == 2) border-orange-500
                @else border-red-500 @endif">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-lg font-bold text-white">Warning {{ $warning->warning_level }}</span>
                            @if($warning->is_active)
                                <span class="px-2 py-0.5 bg-red-500/30 text-red-300 rounded-full text-xs">Active</span>
                            @else
                                <span class="px-2 py-0.5 bg-green-500/30 text-green-300 rounded-full text-xs">Resolved</span>
                            @endif
                            <span class="text-xs text-gray-500">
                                {{ $warning->issued_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        <p class="text-gray-300">
                            <strong class="text-gray-400">Reason:</strong> {{ $warning->reason }}
                        </p>
                        @if($warning->description)
                            <p class="text-sm text-gray-500 mt-2">
                                <strong>Details:</strong> {{ $warning->description }}
                            </p>
                        @endif
                        <div class="flex flex-wrap gap-3 mt-2 text-xs text-gray-500">
                            <span>Issued by: {{ $warning->issuer->name ?? 'System' }}</span>
                            @if($warning->expires_at)
                                <span>Expires: {{ $warning->expires_at->format('d/m/Y H:i') }}</span>
                            @endif
                        </div>
                    </div>
                    @if(auth()->user() && auth()->user()->role === 'admin' && $warning->is_active)
                    <form action="{{ route('participants.remove-warning', [$participant->id, $warning->id]) }}" 
                          method="POST" 
                          onsubmit="return confirm('Yakin ingin menghapus warning ini?')"
                          class="ml-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-300 text-sm transition flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Remove
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        @if($warnings['remaining_blocked_events'] > 0)
        <div class="mt-6 p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-lg">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <p class="text-sm text-yellow-300">
                    ⚠️ Participant masih terblokir untuk <strong>{{ $warnings['remaining_blocked_events'] }} event berikutnya</strong>
                </p>
            </div>
        </div>
        @endif
    @elseif($warnings['current_level'] == 0)
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-green-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-gray-400">No warnings found</p>
            <p class="text-sm text-gray-500 mt-1">This participant has a clean record</p>
        </div>
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-gray-400">No active warnings</p>
            <p class="text-sm text-gray-500 mt-1">All warnings have been resolved</p>
        </div>
    @endif
</div>
@endsection