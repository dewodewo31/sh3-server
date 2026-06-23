@extends('layouts.app')

@section('title', 'Tambah User')
@section('page-title', 'Create New User')
@section('page-description', 'Add a new user to the system')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-white transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Users
        </a>
    </div>

    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-8">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-blue-500 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white">Form Tambah User</h2>
                <p class="text-sm text-gray-400">Isikan data user baru</p>
            </div>
        </div>

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-300 mb-2 font-semibold">Nama Lengkap *</label>
                <input type="text" 
                       name="name" 
                       value="{{ old('name') }}"
                       class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 @error('name') border-red-500 @enderror"
                       required>
                @error('name')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-300 mb-2 font-semibold">Email *</label>
                <input type="email" 
                       name="email" 
                       value="{{ old('email') }}"
                       class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 @error('email') border-red-500 @enderror"
                       required>
                @error('email')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-300 mb-2 font-semibold">Password *</label>
                <input type="password" 
                       name="password" 
                       class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 @error('password') border-red-500 @enderror"
                       required>
                <p class="text-xs text-gray-400 mt-1">Minimal 6 karakter</p>
                @error('password')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-300 mb-2 font-semibold">Konfirmasi Password *</label>
                <input type="password" 
                       name="password_confirmation" 
                       class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500"
                       required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-300 mb-2 font-semibold">Role *</label>
                <select name="role" 
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-black focus:outline-none focus:border-green-500" 
                        required>
                    <option value="" class="text-black">Pilih Role</option>
                    <optgroup label="Admin Roles">
                        <option class="text-black" value="admin" {{ old('role', $user->role ?? '') == 'admin' ? 'selected' : '' }} class="text-black">Admin (Legacy)</option>
                        <option class="text-black" value="admin_full_access" {{ old('role', $user->role ?? '') == 'admin_full_access' ? 'selected' : '' }} class="text-black">Admin Full Access</option>
                        <option class="text-black" value="admin_laman" {{ old('role', $user->role ?? '') == 'admin_laman' ? 'selected' : '' }} class="text-black">Admin Laman</option>
                        <option class="text-black" value="admin_member" {{ old('role', $user->role ?? '') == 'admin_member' ? 'selected' : '' }} class="text-black">Admin Member</option>
                        <option class="text-black" value="admin_bnh" {{ old('role', $user->role ?? '') == 'admin_bnh' ? 'selected' : '' }} class="text-black">Admin BNH</option>
                    </optgroup>
                    <optgroup label="Other Roles">
                        <option class="text-black" value="organizer" {{ old('role', $user->role ?? '') == 'organizer' ? 'selected' : '' }} class="text-black">Organizer</option>
                        <option class="text-black" value="bendahara" {{ old('role', $user->role ?? '') == 'bendahara' ? 'selected' : '' }} class="text-black">Bendahara</option>
                        <option class="text-black" value="sponsor" {{ old('role', $user->role ?? '') == 'sponsor' ? 'selected' : '' }} class="text-black">Sponsor</option>
                        <option class="text-black" value="merchandise" {{ old('role', $user->role ?? '') == 'merchandise' ? 'selected' : '' }} class="text-black">Merchandise</option>
                    </optgroup>
                    <optgroup label="Default">
                        <option value="participant" {{ old('role', $user->role ?? '') == 'participant' ? 'selected' : '' }} class="text-black">Participant</option>
                    </optgroup>
                </select>
                @error('role')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition font-semibold">
                    Simpan User
                </button>
                <a href="{{ route('users.index') }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection