@extends('layouts.app')

@section('title', 'Edit Participant - ' . $participant->name)
@section('page-title', 'Edit Participant')
@section('page-description', 'Update participant information')

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
            <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white">Edit Participant</h2>
                <p class="text-sm text-gray-400">Update data participant: {{ $participant->name }}</p>
            </div>
        </div>

        <form action="{{ route('participants.update', $participant) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-300 mb-2 font-semibold">Hash ID</label>
                <div class="flex items-center gap-2">
                    <code class="flex-1 bg-white/10 border border-white/10 rounded-lg px-4 py-3 text-green-400 font-mono">
                        {{ $participant->hash_id }}
                    </code>
                    <a href="{{ route('participants.regenerate-hash', $participant) }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-3 rounded-lg transition"
                       onclick="return confirm('Yakin ingin mengganti Hash ID? Hash ID lama tidak dapat digunakan lagi.')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
                        </svg>
                    </a>
                </div>
                <p class="text-xs text-gray-400 mt-1">Hash ID digunakan participant untuk login. Regenerate jika perlu</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-300 mb-2 font-semibold">Nama Lengkap *</label>
                <input type="text" 
                       name="name" 
                       value="{{ old('name', $participant->name) }}"
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
                       value="{{ old('email', $participant->email) }}"
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
                       value="{{ old('phone', $participant->phone) }}"
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
                        <option value="male" {{ old('gender', $participant->gender) == 'male' ? 'selected' : '' }} class="text-black">Male</option>
                        <option value="female" {{ old('gender', $participant->gender) == 'female' ? 'selected' : '' }} class="text-black">Female</option>
                    </select>
                    @error('gender')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-gray-300 mb-2 font-semibold">Tanggal Lahir *</label>
                    <input type="date" 
                           name="birthdate" 
                           value="{{ old('birthdate', $participant->birthdate->format('Y-m-d')) }}"
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
                    <option value="active" {{ old('status', $participant->status) == 'active' ? 'selected' : '' }} class="text-black">Active - Bisa login</option>
                    <option value="inactive" {{ old('status', $participant->status) == 'inactive' ? 'selected' : '' }} class="text-black">Inactive - Tidak bisa login</option>
                </select>
                <p class="text-xs text-gray-400 mt-1">Participant yang inactive tidak bisa login menggunakan hash ID</p>
            </div>

            <div class="mb-6">
                <label class="block text-gray-300 mb-2 font-semibold">Catatan (Opsional)</label>
                <textarea name="notes" 
                          rows="3"
                          class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500"
                          placeholder="Catatan khusus untuk participant ini...">{{ old('notes', $participant->notes) }}</textarea>
            </div>

            <!-- Info Card -->
            <div class="bg-white/5 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-400">Participant ID</p>
                        <p class="text-white font-semibold">#{{ $participant->id }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Total Orders</p>
                        <p class="text-green-400 font-semibold">{{ $participant->orders()->count() }} Orders</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Dibuat Pada</p>
                        <p class="text-white">{{ $participant->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Terakhir Update</p>
                        <p class="text-white">{{ $participant->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($participant->last_login_at)
                    <div>
                        <p class="text-gray-400">Last Login</p>
                        <p class="text-white">{{ $participant->last_login_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition font-semibold">
                    Update Participant
                </button>
                <a href="{{ route('participants.index') }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection