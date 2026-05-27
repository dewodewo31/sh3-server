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
                <p class="text-sm text-gray-400">Hash ID akan digenerate otomatis (member: 0001, non-member: 0000)</p>
            </div>
        </div>

        <form action="{{ route('participants.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Tipe Participant -->
            <div class="mb-4">
                <label class="block text-gray-300 mb-2 font-semibold">Tipe Participant *</label>
                <select name="participant_type" id="participant_type" 
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 @error('participant_type') border-red-500 @enderror"
                        required>
                    <option value="non_member" {{ old('participant_type', 'non_member') == 'non_member' ? 'selected' : '' }} class="text-black">Non-Member (Hash ID: 0000)</option>
                    <option value="member" {{ old('participant_type') == 'member' ? 'selected' : '' }} class="text-black">Member (Hash ID: 0001, 0002, ...)</option>
                </select>
                <p class="text-xs text-gray-400 mt-1">Member memiliki hash ID unik 4 digit, Non-Member memiliki hash ID 0000</p>
                @error('participant_type')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

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
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }} class="text-black">Laki-laki</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }} class="text-black">Perempuan</option>
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

            <!-- Medical Information (Collapsible) -->
            <div class="mt-6 mb-4">
                <button type="button" id="toggleMedicalBtn" class="flex items-center gap-2 text-green-400 hover:text-green-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                    <span class="font-semibold">Informasi Medis & Identitas (Opsional)</span>
                </button>
                
                <div id="medicalSection" class="hidden mt-4 space-y-4">
                    <!-- Golongan Darah -->
                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Golongan Darah</label>
                        <select name="blood_type" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500">
                            <option value="" class="text-black">Pilih Golongan Darah</option>
                            <option value="A" {{ old('blood_type') == 'A' ? 'selected' : '' }} class="text-black">A</option>
                            <option value="B" {{ old('blood_type') == 'B' ? 'selected' : '' }} class="text-black">B</option>
                            <option value="AB" {{ old('blood_type') == 'AB' ? 'selected' : '' }} class="text-black">AB</option>
                            <option value="O" {{ old('blood_type') == 'O' ? 'selected' : '' }} class="text-black">O</option>
                        </select>
                    </div>

                    <!-- Emergency Contact -->
                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Kontak Darurat</label>
                        <input type="text" 
                               name="emergency_contact" 
                               value="{{ old('emergency_contact') }}"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500"
                               placeholder="Nama kontak darurat">
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">No. Telepon Darurat</label>
                        <input type="text" 
                               name="emergency_phone" 
                               value="{{ old('emergency_phone') }}"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500"
                               placeholder="Nomor telepon darurat">
                    </div>

                    <!-- Riwayat Alergi -->
                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Riwayat Alergi</label>
                        <textarea name="allergy_history" 
                                  rows="2"
                                  class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500"
                                  placeholder="Contoh: Debu, Udang, Kacang, Obat Penisilin">{{ old('allergy_history') }}</textarea>
                    </div>

                    <!-- Nomor Identitas -->
                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Nomor KTP / Paspor</label>
                        <input type="text" 
                               name="identity_number" 
                               value="{{ old('identity_number') }}"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500"
                               placeholder="Nomor KTP atau Paspor">
                    </div>

                    <!-- Foto Identitas -->
                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Foto KTP / Paspor</label>
                        <input type="file" 
                               name="identity_photo" 
                               accept="image/*"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-green-500 file:text-white file:cursor-pointer hover:file:bg-green-600">
                        <p class="text-xs text-gray-400 mt-1">Format: JPG, JPEG, PNG. Max 2MB</p>
                    </div>

                    <!-- Foto Profil -->
                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Foto Profil</label>
                        <input type="file" 
                               name="photo" 
                               accept="image/*"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-green-500 file:text-white file:cursor-pointer hover:file:bg-green-600">
                        <p class="text-xs text-gray-400 mt-1">Format: JPG, JPEG, PNG. Max 2MB</p>
                    </div>
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

<script>
    // Toggle medical section
    const toggleBtn = document.getElementById('toggleMedicalBtn');
    const medicalSection = document.getElementById('medicalSection');
    
    toggleBtn.addEventListener('click', function() {
        medicalSection.classList.toggle('hidden');
        const arrow = toggleBtn.querySelector('svg');
        if (medicalSection.classList.contains('hidden')) {
            arrow.classList.remove('rotate-180');
        } else {
            arrow.classList.add('rotate-180');
        }
    });
</script>
@endsection