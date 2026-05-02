@extends('layouts.app')

@section('title', 'Tambah Participant')
@section('page-title', 'Create New Participant')
@section('page-description', 'Add a new event participant')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('participants.index') }}" class="text-gray-400 hover:text-white transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Participant
        </a>
    </div>

    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-blue-500 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white">Form Tambah Participant</h2>
                <p class="text-sm text-gray-400">Hash ID akan digenerate otomatis (SH3IDxxxxxx)</p>
            </div>
        </div>

        <form action="{{ route('participants.store') }}" method="POST">
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
                <label class="block text-gray-300 mb-2 font-semibold">No. Telepon *</label>
                <input type="text" 
                       name="phone" 
                       value="{{ old('phone') }}"
                       class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 @error('phone') border-red-500 @enderror"
                       required>
                @error('phone')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-300 mb-2 font-semibold">Jenis Kelamin *</label>
                    <select name="gender" 
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 @error('gender') border-red-500 @enderror"
                            required>
                        <option value="" class="text-black">Pilih</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }} class="text-black">Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }} class="text-black">Female</option>
                    </select>
                    @error('gender')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 mb-2 font-semibold">Tanggal Lahir *</label>
                    <input type="date" 
                           name="birthdate" 
                           value="{{ old('birthdate') }}"
                           class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 @error('birthdate') border-red-500 @enderror"
                           required>
                    @error('birthdate')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-gray-300 mb-2 font-semibold">Status</label>
                <select name="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500">
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }} class="text-black">Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }} class="text-black">Inactive</option>
                </select>
                <p class="text-xs text-gray-400 mt-1">Participant yang inactive tidak bisa login</p>
            </div>

            <div class="mb-6">
                <label class="block text-gray-300 mb-2 font-semibold">Catatan (Opsional)</label>
                <textarea name="notes" 
                          rows="3"
                          class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500"
                          placeholder="Catatan khusus untuk participant ini...">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition font-semibold">
                    Simpan Participant
                </button>
                <a href="{{ route('participants.index') }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection