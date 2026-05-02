@extends('layouts.app')

@section('title', 'Tambah Event')

@section('content')
<div class="mb-6">
    <a href="{{ route('events.index') }}" class="text-gray-400 hover:text-white transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali
    </a>
</div>

<h2 class="text-2xl font-bold text-white mb-6">Tambah Event Baru</h2>

<form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column -->
        <div class="space-y-6">
            <!-- Title -->
            <div>
                <label class="block text-gray-300 mb-2">Judul Event *</label>
                <input type="text" 
                       name="title" 
                       value="{{ old('title') }}"
                       class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500 @error('title') border-red-500 @enderror"
                       placeholder="Masukkan judul event">
                @error('title')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Category -->
            <div>
                <label class="block text-gray-300 mb-2">Kategori *</label>
                <select name="category_id" 
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500 @error('category_id') border-red-500 @enderror">
                    <option value=""  class="text-black">Pilih Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}  class="text-black">
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Location -->
            <div>
                <label class="block text-gray-300 mb-2">Lokasi *</label>
                <input type="text" 
                       name="location" 
                       value="{{ old('location') }}"
                       class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500 @error('location') border-red-500 @enderror"
                       placeholder="Masukkan lokasi event">
                @error('location')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Key Points -->
            <div>
                <label class="block text-gray-300 mb-2">Key Points (Pisahkan dengan Enter)</label>
                <div id="key-points-container">
                    <div class="flex gap-2 mb-2">
                        <input type="text" 
                               name="key_point[]" 
                               class="flex-1 bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500"
                               placeholder="Contoh: Akses gratis parkir">
                        <button type="button" onclick="addKeyPoint()" class="bg-green-500 hover:bg-green-600 px-3 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @error('key_point')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Date Range -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-300 mb-2">Tanggal Mulai *</label>
                    <input type="datetime-local" 
                           name="start_date" 
                           value="{{ old('start_date') }}"
                           class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500 @error('start_date') border-red-500 @enderror">
                    @error('start_date')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-gray-300 mb-2">Tanggal Selesai *</label>
                    <input type="datetime-local" 
                           name="end_date" 
                           value="{{ old('end_date') }}"
                           class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500 @error('end_date') border-red-500 @enderror">
                    @error('end_date')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Price & Quota -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-300 mb-2">Harga</label>
                    <input type="number" 
                           name="price" 
                           value="{{ old('price', 0) }}"
                           step="1000"
                           class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500"
                           placeholder="0 = Gratis">
                    @error('price')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-gray-300 mb-2">Kuota *</label>
                    <input type="number" 
                           name="quota" 
                           value="{{ old('quota') }}"
                           class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500 @error('quota') border-red-500 @enderror"
                           placeholder="Jumlah peserta">
                    @error('quota')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Image -->
            <div>
                <label class="block text-gray-300 mb-2">Gambar Event</label>
                <input type="file" 
                       name="image" 
                       accept="image/*"
                       class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500 cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-green-500 file:text-white file:cursor-pointer hover:file:bg-green-600">
                <p class="text-xs text-gray-400 mt-1">Format: JPG, JPEG, PNG. Max 2MB</p>
                @error('image')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Image Preview -->
            <div id="image-preview" class="hidden">
                <label class="block text-gray-300 mb-2">Preview Gambar</label>
                <img id="preview" class="rounded-lg max-h-48 w-auto">
            </div>
        </div>
    </div>
    
    <!-- Description -->
    <div class="mt-6">
        <label class="block text-gray-300 mb-2">Deskripsi *</label>
        <textarea name="description" 
                  rows="8"
                  class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500 @error('description') border-red-500 @enderror"
                  placeholder="Deskripsi lengkap tentang event">{{ old('description') }}</textarea>
        @error('description')
            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    
    <!-- Submit Button -->
    <div class="mt-6 flex gap-3">
        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition">
            Simpan Event
        </button>
        <a href="{{ route('events.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition">
            Batal
        </a>
    </div>
</form>

<script>
    // Add key point input
    function addKeyPoint() {
        const container = document.getElementById('key-points-container');
        const div = document.createElement('div');
        div.className = 'flex gap-2 mb-2';
        div.innerHTML = `
            <input type="text" 
                   name="key_point[]" 
                   class="flex-1 bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500"
                   placeholder="Key point lainnya">
            <button type="button" onclick="this.parentElement.remove()" class="bg-red-500 hover:bg-red-600 px-3 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>
                </svg>
            </button>
        `;
        container.appendChild(div);
    }
    
    // Image preview
    document.querySelector('input[name="image"]').addEventListener('change', function(e) {
        const preview = document.getElementById('image-preview');
        const img = document.getElementById('preview');
        
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                preview.classList.remove('hidden');
            }
            reader.readAsDataURL(e.target.files[0]);
        } else {
            preview.classList.add('hidden');
        }
    });
</script>
@endsection