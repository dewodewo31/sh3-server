@extends('layouts.app')

@section('title', 'Tambah Jabatan')
@section('page-title', 'Tambah Jabatan')
@section('page-description', 'Tambahkan jabatan baru ke struktur organisasi')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('organization.index') }}" class="text-gray-400 hover:text-white transition flex items-center gap-2 text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Struktur Organisasi
        </a>
    </div>

    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6 md:p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-blue-500 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white">Tambah Jabatan Baru</h2>
                <p class="text-sm text-gray-400">Isikan data jabatan dan pemegang jabatan</p>
            </div>
        </div>

        <form action="{{ route('organization.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-5">
                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Tahun *</label>
                        <select name="year" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500" required>
                            @foreach($years as $y)
                                <option class="text-black" value="{{ $y }}" {{ old('year', date('Y')) == $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endforeach
                        </select>
                        @error('year')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Level *</label>
                        <select name="level" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500" required>
                            <option class="text-black" value="">Pilih Level</option>
                            @foreach($levels as $levelKey => $levelName)
                                <option class="text-black" value="{{ $levelKey }}" {{ old('level') == $levelKey ? 'selected' : '' }}>
                                    {{ $levelName }}
                                </option>
                            @endforeach
                        </select>
                        @error('level')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Nama Jabatan *</label>
                        <input type="text" 
                               name="position_name" 
                               value="{{ old('position_name') }}"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500"
                               placeholder="Contoh: Ketua, Sekretaris, Bendahara"
                               required>
                        @error('position_name')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Nama Level (Opsional)</label>
                        <input type="text" 
                               name="level_name" 
                               value="{{ old('level_name') }}"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500"
                               placeholder="Contoh: Pengurus Inti, Bidang, Seksi">
                        @error('level_name')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-5">
                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Kode Jabatan (Opsional)</label>
                        <input type="text" 
                               name="position_code" 
                               value="{{ old('position_code') }}"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500"
                               placeholder="Contoh: HM-001">
                        @error('position_code')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Jabatan Induk (Opsional)</label>
                        <select name="parent_id" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500">
                            <option class="text-black" value="">Tidak Ada</option>
                            @foreach($parents as $parent)
                                <option class="text-black" value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->position_name }} (Level {{ $parent->level }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Pilih jabatan di atasnya dalam hierarki</p>
                        @error('parent_id')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Urutan (Opsional)</label>
                        <input type="number" 
                               name="sort_order" 
                               value="{{ old('sort_order', 0) }}"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500"
                               min="0">
                        @error('sort_order')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-4 h-4 rounded bg-white/5 border-white/10">
                        <label for="is_active" class="text-gray-300">Aktifkan jabatan</label>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <label class="block text-gray-300 mb-2 font-semibold">Deskripsi (Opsional)</label>
                <textarea name="description" 
                          rows="3"
                          class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500"
                          placeholder="Deskripsi tentang jabatan ini...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4">
                <label class="block text-gray-300 mb-2 font-semibold">Tanggung Jawab (Opsional)</label>
                <textarea name="responsibilities" 
                          rows="3"
                          class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500"
                          placeholder="Tugas dan tanggung jawab...">{{ old('responsibilities') }}</textarea>
                @error('responsibilities')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Holder Section -->
            <div class="mt-6 p-5 bg-white/5 rounded-lg border border-white/10">
                <h4 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Pemegang Jabatan (Opsional)
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-300 mb-2 text-sm">Nama Pemegang</label>
                        <input type="text" 
                               name="holder_name" 
                               value="{{ old('holder_name') }}"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500"
                               placeholder="Nama lengkap">
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2 text-sm">Nama Panggilan</label>
                        <input type="text" 
                               name="holder_nickname" 
                               value="{{ old('holder_nickname') }}"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500"
                               placeholder="Nama panggilan">
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2 text-sm">Email</label>
                        <input type="email" 
                               name="holder_email" 
                               value="{{ old('holder_email') }}"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500"
                               placeholder="email@example.com">
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2 text-sm">Telepon</label>
                        <input type="text" 
                               name="holder_phone" 
                               value="{{ old('holder_phone') }}"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500"
                               placeholder="081234567890">
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2 text-sm">Anggota Sejak (Tahun)</label>
                        <input type="number" 
                               name="holder_member_since" 
                               value="{{ old('holder_member_since') }}"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500"
                               placeholder="2020" min="2000" max="{{ date('Y') }}">
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2 text-sm">Periode Mulai</label>
                        <input type="number" 
                               name="holder_period_start" 
                               value="{{ old('holder_period_start') }}"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500"
                               placeholder="2024" min="2000">
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2 text-sm">Periode Selesai</label>
                        <input type="number" 
                               name="holder_period_end" 
                               value="{{ old('holder_period_end') }}"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500"
                               placeholder="2025" min="2000">
                    </div>
                    <div>
                        <label class="block text-gray-300 mb-2 text-sm">Bio Singkat</label>
                        <textarea name="holder_bio" 
                                  rows="2"
                                  class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500"
                                  placeholder="Bio singkat...">{{ old('holder_bio') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 mt-6">
                <button type="submit" class="flex-1 bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition font-semibold">
                    Simpan Jabatan
                </button>
                <a href="{{ route('organization.index') }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection