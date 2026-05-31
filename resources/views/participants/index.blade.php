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
        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
        </svg>
        <p class="text-gray-400 text-sm">Members</p>
    </div>
    <h3 class="text-2xl font-bold text-purple-400">{{ $memberCount ?? 0 }}</h3>
</div>

<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
        <p class="text-gray-400 text-sm">Non-Members</p>
    </div>
    <h3 class="text-2xl font-bold text-gray-400">{{ $nonMemberCount ?? 0 }}</h3>
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
<div class="flex justify-between items-center mb-6 flex-wrap gap-4">
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

<!-- FILTERS & SEARCH SECTION -->
<div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-4 mb-6">
    <form method="GET" action="{{ route('participants.index') }}" class="flex flex-wrap gap-3 items-end">
        <!-- Search Input -->
        <div class="flex-1 min-w-[200px]">
            <label class="block text-gray-400 text-sm mb-1">Search</label>
            <div class="relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Search by name, email, phone, or hash ID..."
                       class="w-full bg-black/30 border border-white/10 rounded-lg pl-10 pr-4 py-2 text-white focus:outline-none focus:border-green-500">
            </div>
        </div>

        <!-- Participant Type Filter -->
        <div>
            <label class="block text-gray-400 text-sm mb-1">Type</label>
            <select name="type" class="bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500">
                <option value="" class="text-black">All Types</option>
                <option value="member" {{ request('type') == 'member' ? 'selected' : '' }} class="text-black">Member</option>
                <option value="non_member" {{ request('type') == 'non_member' ? 'selected' : '' }} class="text-black">Non-Member</option>
            </select>
        </div>

        <!-- Status Filter -->
        <div>
            <label class="block text-gray-400 text-sm mb-1">Status</label>
            <select name="status" class="bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500">
                <option value="" class="text-black">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }} class="text-black">Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }} class="text-black">Inactive</option>
            </select>
        </div>

        <!-- Gender Filter -->
        <div>
            <label class="block text-gray-400 text-sm mb-1">Gender</label>
            <select name="gender" class="bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500">
                <option value="" class="text-black">All Gender</option>
                <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }} class="text-black">Male</option>
                <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }} class="text-black">Female</option>
            </select>
        </div>

        <!-- Warning Level Filter -->
        <div>
            <label class="block text-gray-400 text-sm mb-1">Warning Level</label>
            <select name="warning_level" class="bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500">
                <option value="" class="text-black">All</option>
                <option value="0" {{ request('warning_level') == '0' ? 'selected' : '' }} class="text-black">Clean (No Warning)</option>
                <option value="1" {{ request('warning_level') == '1' ? 'selected' : '' }} class="text-black">Warning 1</option>
                <option value="2" {{ request('warning_level') == '2' ? 'selected' : '' }} class="text-black">Warning 2</option>
                <option value="3" {{ request('warning_level') == '3' ? 'selected' : '' }} class="text-black">Warning 3 (Suspended)</option>
            </select>
        </div>

        <!-- Sort By -->
        <div>
            <label class="block text-gray-400 text-sm mb-1">Sort By</label>
            <select name="sort" class="bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500">
                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }} class="text-black">Latest Registered</option>
                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }} class="text-black">Oldest Registered</option>
                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }} class="text-black">Name A-Z</option>
                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }} class="text-black">Name Z-A</option>
            </select>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-2">
            <button type="submit" class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded-lg transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Filter
            </button>
            
            @if(request()->hasAny(['search', 'type', 'status', 'gender', 'warning_level', 'sort']))
            <a href="{{ route('participants.index') }}" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Reset
            </a>
            @endif
        </div>
    </form>
</div>

<!-- Participants Table - Grouped by Type -->
@php
    $memberParticipants = $participants->where('participant_type', 'member');
    $nonMemberParticipants = $participants->where('participant_type', 'non_member');
@endphp

<!-- MEMBER FOLDER -->
<div class="mb-6">
    <div class="flex items-center gap-3 mb-3 bg-gradient-to-r from-purple-500/10 to-purple-600/5 p-3 rounded-lg border border-purple-500/20">
        <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>
        <div>
            <h3 class="text-lg font-bold text-purple-400">Member</h3>
            <p class="text-xs text-gray-400">{{ $memberParticipants->count() }} participants</p>
        </div>
        <div class="ml-auto">
            <span class="px-3 py-1 bg-purple-500/20 text-purple-300 rounded-full text-xs font-semibold">
                {{ $memberParticipants->count() }} items
            </span>
        </div>
    </div>

    <div class="overflow-x-auto bg-white/5 rounded-lg border border-white/10">
        <table class="w-full">
            <thead>
                <tr class="border-b border-white/10 bg-white/5">
                    <th class="text-left py-2 px-3 text-gray-400 text-xs">Hash ID</th>
                    <th class="text-left py-2 px-3 text-gray-400 text-xs">Name</th>
                    <th class="text-left py-2 px-3 text-gray-400 text-xs">Contact</th>
                    <th class="text-left py-2 px-3 text-gray-400 text-xs">Gender</th>
                    <th class="text-left py-2 px-3 text-gray-400 text-xs">Birthdate</th>
                    <th class="text-left py-2 px-3 text-gray-400 text-xs">Warning</th>
                    <th class="text-left py-2 px-3 text-gray-400 text-xs">Status</th>
                    <th class="text-center py-2 px-3 text-gray-400 text-xs">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($memberParticipants as $participant)
                <tr class="border-b border-white/10 hover:bg-white/5 transition
                    @if($participant->is_suspended) bg-red-500/5 @endif
                    @if($participant->current_warning_level == 1) bg-yellow-500/5 @endif
                    @if($participant->current_warning_level == 2) bg-orange-500/5 @endif">

                    <td class="py-2 px-3">
                        <code class="text-purple-400 font-mono text-xs">{{ $participant->hash_id }}</code>
                    </td>

                    <td class="py-2 px-3">
                        <div class="flex items-center gap-2">
                            @if($participant->photo)
                            <img src="{{ Storage::url($participant->photo) }}" class="w-7 h-7 rounded-full object-cover">
                            @else
                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-gray-600 to-gray-700 flex items-center justify-center text-xs font-bold">
                                {{ strtoupper(substr($participant->name, 0, 1)) }}
                            </div>
                            @endif
                            <div>
                                <p class="text-sm font-semibold">{{ $participant->name }}</p>
                                <p class="text-xs text-gray-400">{{ $participant->email }}</p>
                            </div>
                        </div>
                    </td>

                    <td class="py-2 px-3">
                        <p class="text-xs">{{ $participant->phone }}</p>
                    </td>

                    <td class="py-2 px-3">
                        @if($participant->gender == 'male')
                            <span class="px-2 py-0.5 bg-blue-500/20 text-blue-300 rounded-full text-xs">Male</span>
                        @else
                            <span class="px-2 py-0.5 bg-pink-500/20 text-pink-300 rounded-full text-xs">Female</span>
                        @endif
                    </td>

                    <td class="py-2 px-3 text-xs">
                        {{ $participant->birthdate->format('d M Y') }}
                        <span class="text-xs text-gray-500 block">{{ $participant->birthdate->age }} years</span>
                    </td>

                    <td class="py-2 px-3">
                        @if($participant->current_warning_level == 0)
                            <span class="px-2 py-0.5 bg-green-500/20 text-green-300 rounded-full text-xs flex items-center gap-1 w-fit">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Clean
                            </span>
                        @elseif($participant->current_warning_level == 1)
                            <span class="px-2 py-0.5 bg-yellow-500/20 text-yellow-300 rounded-full text-xs flex items-center gap-1 w-fit">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                Warning 1
                            </span>
                        @elseif($participant->current_warning_level == 2)
                            <span class="px-2 py-0.5 bg-orange-500/20 text-orange-300 rounded-full text-xs flex items-center gap-1 w-fit">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                Warning 2
                            </span>
                        @elseif($participant->current_warning_level >= 3)
                            <span class="px-2 py-0.5 bg-red-500/20 text-red-300 rounded-full text-xs flex items-center gap-1 w-fit">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                Suspended
                            </span>
                        @endif
                    </td>

                    <td class="py-2 px-3">
                        @if($participant->status == 'active')
                            <span class="px-2 py-0.5 bg-green-500/20 text-green-300 rounded-full text-xs">Active</span>
                        @else
                            <span class="px-2 py-0.5 bg-red-500/20 text-red-300 rounded-full text-xs">Inactive</span>
                        @endif
                    </td>

                    <td class="py-2 px-3">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('participants.show', $participant) }}" class="text-blue-400 hover:text-blue-300 transition" title="View Details">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </a>
                            <a href="{{ route('participants.edit', $participant) }}" class="text-yellow-400 hover:text-yellow-300 transition" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </a>
                            <form action="{{ route('participants.destroy', $participant) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin ingin menghapus participant ini?')" class="text-red-400 hover:text-red-300 transition" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-6 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                        <p class="text-sm">No member participants found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- NON-MEMBER FOLDER -->
<div class="mb-6">
    <div class="flex items-center gap-3 mb-3 bg-gradient-to-r from-gray-500/10 to-gray-600/5 p-3 rounded-lg border border-gray-500/20">
        <div class="w-10 h-10 bg-gray-500/20 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
        </div>
        <div>
            <h3 class="text-lg font-bold text-gray-400">Non-Member</h3>
            <p class="text-xs text-gray-400">{{ $nonMemberParticipants->count() }} participants</p>
        </div>
        <div class="ml-auto">
            <span class="px-3 py-1 bg-gray-500/20 text-gray-300 rounded-full text-xs font-semibold">
                {{ $nonMemberParticipants->count() }} items
            </span>
        </div>
    </div>

    <div class="overflow-x-auto bg-white/5 rounded-lg border border-white/10">
        <table class="w-full">
            <thead>
                <tr class="border-b border-white/10 bg-white/5">
                    <th class="text-left py-2 px-3 text-gray-400 text-xs">Hash ID</th>
                    <th class="text-left py-2 px-3 text-gray-400 text-xs">Name</th>
                    <th class="text-left py-2 px-3 text-gray-400 text-xs">Contact</th>
                    <th class="text-left py-2 px-3 text-gray-400 text-xs">Gender</th>
                    <th class="text-left py-2 px-3 text-gray-400 text-xs">Birthdate</th>
                    <th class="text-left py-2 px-3 text-gray-400 text-xs">Warning</th>
                    <th class="text-left py-2 px-3 text-gray-400 text-xs">Status</th>
                    <th class="text-center py-2 px-3 text-gray-400 text-xs">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($nonMemberParticipants as $participant)
                <tr class="border-b border-white/10 hover:bg-white/5 transition
                    @if($participant->is_suspended) bg-red-500/5 @endif
                    @if($participant->current_warning_level == 1) bg-yellow-500/5 @endif
                    @if($participant->current_warning_level == 2) bg-orange-500/5 @endif">

                    <td class="py-2 px-3">
                        <code class="text-green-400 font-mono text-xs">{{ $participant->hash_id }}</code>
                    </td>

                    <td class="py-2 px-3">
                        <div class="flex items-center gap-2">
                            @if($participant->photo)
                            <img src="{{ Storage::url($participant->photo) }}" class="w-7 h-7 rounded-full object-cover">
                            @else
                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-gray-600 to-gray-700 flex items-center justify-center text-xs font-bold">
                                {{ strtoupper(substr($participant->name, 0, 1)) }}
                            </div>
                            @endif
                            <div>
                                <p class="text-sm font-semibold">{{ $participant->name }}</p>
                                <p class="text-xs text-gray-400">{{ $participant->email }}</p>
                            </div>
                        </div>
                    </td>

                    <td class="py-2 px-3">
                        <p class="text-xs">{{ $participant->phone }}</p>
                    </td>

                    <td class="py-2 px-3">
                        @if($participant->gender == 'male')
                            <span class="px-2 py-0.5 bg-blue-500/20 text-blue-300 rounded-full text-xs">Male</span>
                        @else
                            <span class="px-2 py-0.5 bg-pink-500/20 text-pink-300 rounded-full text-xs">Female</span>
                        @endif
                    </td>

                    <td class="py-2 px-3 text-xs">
                        {{ $participant->birthdate->format('d M Y') }}
                        <span class="text-xs text-gray-500 block">{{ $participant->birthdate->age }} years</span>
                    </td>

                    <td class="py-2 px-3">
                        @if($participant->current_warning_level == 0)
                            <span class="px-2 py-0.5 bg-green-500/20 text-green-300 rounded-full text-xs flex items-center gap-1 w-fit">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Clean
                            </span>
                        @elseif($participant->current_warning_level == 1)
                            <span class="px-2 py-0.5 bg-yellow-500/20 text-yellow-300 rounded-full text-xs flex items-center gap-1 w-fit">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                Warning 1
                            </span>
                        @elseif($participant->current_warning_level == 2)
                            <span class="px-2 py-0.5 bg-orange-500/20 text-orange-300 rounded-full text-xs flex items-center gap-1 w-fit">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                Warning 2
                            </span>
                        @elseif($participant->current_warning_level >= 3)
                            <span class="px-2 py-0.5 bg-red-500/20 text-red-300 rounded-full text-xs flex items-center gap-1 w-fit">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                Suspended
                            </span>
                        @endif
                    </td>

                    <td class="py-2 px-3">
                        @if($participant->status == 'active')
                            <span class="px-2 py-0.5 bg-green-500/20 text-green-300 rounded-full text-xs">Active</span>
                        @else
                            <span class="px-2 py-0.5 bg-red-500/20 text-red-300 rounded-full text-xs">Inactive</span>
                        @endif
                    </td>

                    <td class="py-2 px-3">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('participants.show', $participant) }}" class="text-blue-400 hover:text-blue-300 transition" title="View Details">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </a>
                            <a href="{{ route('participants.edit', $participant) }}" class="text-yellow-400 hover:text-yellow-300 transition" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </a>
                            <a href="{{ route('participants.upgrade-to-member', $participant) }}" class="text-green-400 hover:text-green-300 transition" onclick="return confirm('Yakin ingin upgrade participant ini menjadi member? Hash ID akan berubah menjadi format member (4 digit angka)')" title="Upgrade to Member">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            </a>
                            <form action="{{ route('participants.destroy', $participant) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin ingin menghapus participant ini?')" class="text-red-400 hover:text-red-300 transition" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-6 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        <p class="text-sm">No non-member participants found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div><!-- Result Info -->
<div class="mt-4 text-sm text-gray-400">
    Showing {{ $participants->firstItem() ?? 0 }} to {{ $participants->lastItem() ?? 0 }} of {{ $participants->total() }} participants
</div>

<!-- Pagination -->
@if($participants->hasPages())
<div class="mt-6">
    {{ $participants->appends(request()->query())->links() }}
</div>
@endif
@endsection