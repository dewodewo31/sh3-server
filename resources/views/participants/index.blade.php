@extends('layouts.app')

@section('title', 'Participant Management')
@section('page-title', 'Participants')
@section('page-description', 'Manage all event participants')

@section('stats')
<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <p class="text-gray-400 text-sm">Total Participants</p>
    </div>
    <h3 class="text-2xl font-bold text-green-400">{{ $totalParticipants }}</h3>
</div>

<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-gray-400 text-sm">Active</p>
    </div>
    <h3 class="text-2xl font-bold text-green-400">{{ $activeParticipants }}</h3>
</div>

<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
        </svg>
        <p class="text-gray-400 text-sm">Inactive</p>
    </div>
    <h3 class="text-2xl font-bold text-red-400">{{ $inactiveParticipants }}</h3>
</div>

<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        <p class="text-gray-400 text-sm">Male</p>
    </div>
    <h3 class="text-2xl font-bold text-blue-400">{{ $maleParticipants }}</h3>
</div>

<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-pink-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        <p class="text-gray-400 text-sm">Female</p>
    </div>
    <h3 class="text-2xl font-bold text-pink-400">{{ $femaleParticipants }}</h3>
</div>

<a href="{{ route('participants.export-all-pdf') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
    </svg>
    Export All PDF
</a>
@endsection

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-white">Participant Management</h2>
    <a href="{{ route('participants.create') }}"
        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Participant
    </a>
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

<!-- Participants Table -->
<div class="overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="border-b border-white/10">
                <th class="text-left py-3 px-4 text-gray-400">Hash ID</th>
                <th class="text-left py-3 px-4 text-gray-400">Name</th>
                <th class="text-left py-3 px-4 text-gray-400">Contact</th>
                <th class="text-left py-3 px-4 text-gray-400">Gender</th>
                <th class="text-left py-3 px-4 text-gray-400">Birthdate</th>
                <th class="text-left py-3 px-4 text-gray-400">Status</th>
                <th class="text-center py-3 px-4 text-gray-400">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($participants as $participant)
            <tr class="border-b border-white/10 hover:bg-white/5 transition">
                <td class="py-3 px-4">
                    <code class="text-green-400 font-mono text-sm">{{ $participant->hash_id }}</code>
                </td>
                <td class="py-3 px-4">
                    <div>
                        <p class="font-semibold">{{ $participant->name }}</p>
                        <p class="text-xs text-gray-400">{{ $participant->email }}</p>
                    </div>
                </td>
                <td class="py-3 px-4">
                    <p class="text-sm">{{ $participant->phone }}</p>
                </td>
                <td class="py-3 px-4">
                    @if($participant->gender == 'male')
                    <span class="px-2 py-1 bg-blue-500/20 text-blue-300 rounded-full text-xs">Male</span>
                    @else
                    <span class="px-2 py-1 bg-pink-500/20 text-pink-300 rounded-full text-xs">Female</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-sm">
                    {{ $participant->birthdate->format('d M Y') }}
                </td>
                <td class="py-3 px-4">
                    @if($participant->status == 'active')
                    <span class="px-2 py-1 bg-green-500/20 text-green-300 rounded-full text-xs">Active</span>
                    @else
                    <span class="px-2 py-1 bg-red-500/20 text-red-300 rounded-full text-xs">Inactive</span>
                    @endif
                </td>
                <td class="py-3 px-4">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('participants.show', $participant) }}"
                            class="text-blue-400 hover:text-blue-300 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                        <a href="{{ route('participants.edit', $participant) }}"
                            class="text-yellow-400 hover:text-yellow-300 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        <form action="{{ route('participants.destroy', $participant) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                onclick="return confirm('Yakin ingin menghapus participant ini?')"
                                class="text-red-400 hover:text-red-300 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-8 text-center text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <p>No participants found</p>
                    <a href="{{ route('participants.create') }}" class="text-green-400 hover:text-green-300 mt-2 inline-block">
                        Tambah participant pertama
                    </a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($participants->hasPages())
<div class="mt-6">
    {{ $participants->links() }}
</div>
@endif
@endsection