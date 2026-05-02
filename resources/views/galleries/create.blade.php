@extends('layouts.app')

@section('title', 'Tambah Gallery')
@section('page-title', 'Create New Gallery')
@section('page-description', 'Add images to event gallery')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('galleries.index') }}" class="text-gray-400 hover:text-white transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Gallery
        </a>
    </div>

    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-blue-500 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white">Tambah Gallery Baru</h2>
                <p class="text-sm text-gray-400">Upload multiple images untuk event</p>
            </div>
        </div>

        <form action="{{ route('galleries.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-6">
                <label class="block text-gray-300 mb-2 font-semibold">Pilih Event *</label>
                <select name="event_id" id="event_id" 
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 @error('event_id') border-red-500 @enderror"
                        required>
                    <option value="" class="text-black">-- Pilih Event --</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }} class="text-black">
                            {{ $event->title }} - {{ $event->start_date->format('d M Y') }}
                        </option>
                    @endforeach
                </select>
                @error('event_id')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-300 mb-2 font-semibold">Upload Gambar (Max 4 file)</label>
                <div class="border-2 border-dashed border-white/20 rounded-lg p-6 text-center hover:border-green-500 transition cursor-pointer"
                     onclick="document.getElementById('images').click()">
                    <input type="file" 
                           name="images[]" 
                           id="images" 
                           class="hidden" 
                           multiple 
                           accept="image/jpeg,image/jpg,image/png"
                           onchange="previewImages(this)">
                    
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-400">Klik atau drag & drop untuk upload gambar</p>
                    <p class="text-xs text-gray-500 mt-1">Supported: JPG, JPEG, PNG (Max 2MB per file)</p>
                    <p class="text-xs text-gray-500">Maksimal 10 gambar</p>
                </div>
                @error('images')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
                @error('images.*')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Preview Area -->
            <div id="previewArea" class="mb-6 hidden">
                <label class="block text-gray-300 mb-2 font-semibold">Preview Gambar:</label>
                <div id="previewContainer" class="grid grid-cols-2 md:grid-cols-4 gap-4"></div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition font-semibold">
                    Simpan Gallery
                </button>
                <a href="{{ route('galleries.index') }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function previewImages(input) {
        const previewArea = document.getElementById('previewArea');
        const previewContainer = document.getElementById('previewContainer');
        
        previewContainer.innerHTML = '';
        
        if (input.files && input.files.length > 0) {
            previewArea.classList.remove('hidden');
            
            Array.from(input.files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'relative group';
                        
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'w-full h-32 object-cover rounded-lg';
                        
                        const name = document.createElement('p');
                        name.className = 'text-xs text-gray-400 mt-1 truncate';
                        name.textContent = file.name;
                        
                        div.appendChild(img);
                        div.appendChild(name);
                        previewContainer.appendChild(div);
                    }
                    
                    reader.readAsDataURL(file);
                }
            });
        } else {
            previewArea.classList.add('hidden');
        }
    }
</script>
@endpush
@endsection