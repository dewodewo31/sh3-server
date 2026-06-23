@extends('layouts.app')

@section('title', 'User Management')
@section('page-title', 'Users')
@section('page-description', 'Manage all system users')

@section('stats')
<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <p class="text-gray-400 text-sm">Total Users</p>
    </div>
    <h3 class="text-2xl font-bold text-green-400">{{ $totalUsers }}</h3>
</div>

<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
        </svg>
        <p class="text-gray-400 text-sm">Admin</p>
    </div>
    <h3 class="text-2xl font-bold text-purple-400">{{ $totalAdmins }}</h3>
</div>

<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
        <p class="text-gray-400 text-sm">Organizer</p>
    </div>
    <h3 class="text-2xl font-bold text-yellow-400">{{ $totalOrganizers }}</h3>
</div>
@endsection

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-white">User Management</h2>
    <a href="{{ route('users.create') }}"
        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
        Tambah User
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

<!-- Users Table -->
<div class="overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="border-b border-white/10">
                <th class="text-left py-3 px-4 text-gray-400">ID</th>
                <th class="text-left py-3 px-4 text-gray-400">Name</th>
                <th class="text-left py-3 px-4 text-gray-400">Email</th>
                <th class="text-left py-3 px-4 text-gray-400">Role</th>
                <th class="text-left py-3 px-4 text-gray-400">Joined</th>
                <th class="text-center py-3 px-4 text-gray-400">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
            <tr class="border-b border-white/10 hover:bg-white/5 transition">
                <td class="py-3 px-4">#{{ $user->id }}</td>
                <td class="py-3 px-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center">
                            <span class="text-white font-bold text-sm">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                        </div>
                        <span class="font-semibold">{{ $user->name }}</span>
                    </div>
                </td>
                <td class="py-3 px-4">{{ $user->email }}</td>
                <td class="py-3 px-4">
                    @php
                    $roleBadges = [
                    'admin' => ['bg' => 'bg-purple-500/20', 'text' => 'text-purple-300', 'label' => 'Admin'],
                    'admin_full_access' => ['bg' => 'bg-purple-600/20', 'text' => 'text-purple-400', 'label' => 'Admin Full'],
                    'admin_laman' => ['bg' => 'bg-purple-400/20', 'text' => 'text-purple-300', 'label' => 'Admin Laman'],
                    'admin_member' => ['bg' => 'bg-indigo-500/20', 'text' => 'text-indigo-300', 'label' => 'Admin Member'],
                    'admin_bnh' => ['bg' => 'bg-pink-500/20', 'text' => 'text-pink-300', 'label' => 'Admin BNH'],
                    'organizer' => ['bg' => 'bg-yellow-500/20', 'text' => 'text-yellow-300', 'label' => 'Organizer'],
                    'bendahara' => ['bg' => 'bg-green-500/20', 'text' => 'text-green-300', 'label' => 'Bendahara'],
                    'sponsor' => ['bg' => 'bg-blue-500/20', 'text' => 'text-blue-300', 'label' => 'Sponsor'],
                    'merchandise' => ['bg' => 'bg-orange-500/20', 'text' => 'text-orange-300', 'label' => 'Merchandise'],
                    'participant' => ['bg' => 'bg-gray-500/20', 'text' => 'text-gray-300', 'label' => 'Participant'],
                    ];
                    $badge = $roleBadges[$user->role] ?? $roleBadges['participant'];
                    @endphp
                    <span class="px-2 py-1 {{ $badge['bg'] }} {{ $badge['text'] }} rounded-full text-xs">
                        {{ $badge['label'] }}
                    </span>
                </td>
                <td class="py-3 px-4 text-sm">{{ $user->created_at->format('d M Y') }}</td>
                <td class="py-3 px-4">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('users.show', $user) }}"
                            class="text-blue-400 hover:text-blue-300 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                        <a href="{{ route('users.edit', $user) }}"
                            class="text-yellow-400 hover:text-yellow-300 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                onclick="return confirm('Yakin ingin menghapus user {{ $user->name }}?')"
                                class="text-red-400 hover:text-red-300 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-8 text-center text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <p>No users found</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($users->hasPages())
<div class="mt-6">
    {{ $users->links() }}
</div>
@endif
@endsection