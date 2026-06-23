@extends('layouts.app')

@section('title', 'User Details - ' . $user->name)
@section('page-title', 'User Details')
@section('page-description', 'View user information and activities')

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
                <div class="mb-3">
                    {!! $user->role_badge !!}
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

</div>
@endsection