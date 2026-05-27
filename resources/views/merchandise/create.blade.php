@extends('layouts.app')

@section('title', 'Tambah Merchandise')
@section('page-title', 'Create New Product')
@section('page-description', 'Add new merchandise product')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('merchandise.index') }}" class="text-gray-400 hover:text-white transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Produk
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
                <h2 class="text-2xl font-bold text-white">Tambah Produk Baru</h2>
                <p class="text-sm text-gray-400">Isikan data merchandise baru</p>
            </div>
        </div>

        <form action="{{ route('merchandise.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Nama Produk *</label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name') }}"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 @error('name') border-red-500 @enderror"
                               required>
                        @error('name')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Kategori *</label>
                        <select name="category" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500" required>
                            <option class="text-black" value="">Pilih Kategori</option>
                            <option class="text-black" value="clothing" {{ old('category') == 'clothing' ? 'selected' : '' }}>Clothing</option>
                            <option class="text-black" value="accessories" {{ old('category') == 'accessories' ? 'selected' : '' }}>Accessories</option>
                            <option class="text-black" value="collectibles" {{ old('category') == 'collectibles' ? 'selected' : '' }}>Collectibles</option>
                            <option class="text-black" value="others" {{ old('category') == 'others' ? 'selected' : '' }}>Others</option>
                        </select>
                        @error('category')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Harga (Rp) *</label>
                        <input type="number" 
                               name="price" 
                               value="{{ old('price') }}"
                               step="1000"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 @error('price') border-red-500 @enderror"
                               required>
                        @error('price')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Stok *</label>
                        <input type="number" 
                               name="stock" 
                               value="{{ old('stock') }}"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 @error('stock') border-red-500 @enderror"
                               required>
                        @error('stock')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Gambar Produk</label>
                        <input type="file" 
                               name="image" 
                               accept="image/*"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-green-500 file:text-white file:cursor-pointer hover:file:bg-green-600">
                        <p class="text-xs text-gray-400 mt-1">Format: JPG, JPEG, PNG. Max 2MB</p>
                        @error('image')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="imagePreview" class="hidden">
                        <img id="preview" class="w-32 h-32 rounded-lg object-cover border border-green-500">
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Ukuran (Size)</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach($sizes as $size)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="sizes[]" value="{{ $size }}" class="rounded bg-white/5 border-white/10">
                                    <span class="text-gray-300">{{ $size }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Pilih ukuran yang tersedia</p>
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Warna (Color)</label>
                        <div class="flex flex-wrap gap-3">
                            @foreach($colors as $color)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="colors[]" value="{{ $color }}" class="rounded bg-white/5 border-white/10">
                                    <span class="text-gray-300">{{ $color }}</span>
                                </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Pilih warna yang tersedia</p>
                    </div>

                    <!-- Event Selection with Special Price and Stock -->
                    <div x-data="{ 
                        selectedEvent: '{{ old('event_id') }}',
                        discountPrice: '{{ old('event_discount_price') }}',
                        eventStock: '{{ old('event_stock') }}'
                    }">
                        <label class="block text-gray-300 mb-2 font-semibold">Event (Opsional)</label>
                        <select name="event_id" x-model="selectedEvent" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500">
                            <option class="text-black" value="">-- Tanpa Event (Produk Umum) --</option>
                            @foreach($events as $event)
                                <option class="text-black" value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>
                                    {{ $event->title }} 
                                    ({{ $event->start_date->format('d M Y') }} - {{ $event->end_date->format('d M Y') }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-400 mt-1">
                            Pilih event jika produk ini khusus untuk event tertentu. 
                            Kosongkan jika produk dijual secara umum.
                        </p>
                        
                        <!-- Form untuk Harga Khusus Event -->
                        <div x-show="selectedEvent" class="mt-4 space-y-3" x-transition>
                            <div class="p-4 bg-blue-500/10 rounded-lg border border-blue-500/30">
                                <h4 class="text-sm font-semibold text-blue-300 mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Pengaturan Khusus Event
                                </h4>
                                
                                <!-- Informasi Harga Normal -->
                                <div class="mb-3 p-2 bg-white/5 rounded text-sm">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-400">Harga Normal:</span>
                                        <span class="text-white font-semibold" id="normalPriceDisplay">Rp 0</span>
                                    </div>
                                    <div class="flex justify-between items-center mt-1">
                                        <span class="text-gray-400">Stok Normal:</span>
                                        <span class="text-white font-semibold" id="normalStockDisplay">0 pcs</span>
                                    </div>
                                </div>
                                
                                <!-- Harga Khusus -->
                                <div class="mb-3">
                                    <label class="block text-gray-300 text-sm mb-1">Harga Khusus Event (Opsional)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">Rp</span>
                                        <input type="number" 
                                               name="event_discount_price"
                                               x-model="discountPrice"
                                               class="w-full bg-black/30 border border-white/10 rounded-lg pl-10 pr-4 py-2 text-white focus:outline-none focus:border-green-500"
                                               placeholder="Kosongkan untuk harga normal">
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">
                                        Isi jika ingin harga khusus untuk event ini
                                    </p>
                                </div>
                                
                                <!-- Stok Khusus Event -->
                                <div>
                                    <label class="block text-gray-300 text-sm mb-1">Stok Khusus Event (Opsional)</label>
                                    <input type="number" 
                                           name="event_stock"
                                           x-model="eventStock"
                                           class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500"
                                           placeholder="Kosongkan untuk stok normal">
                                    <p class="text-xs text-gray-400 mt-1">
                                        Isi jika ingin stok terbatas untuk event ini
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-4 h-4 rounded bg-white/5 border-white/10">
                        <label for="is_active" class="text-gray-300">Aktifkan produk</label>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <label class="block text-gray-300 mb-2 font-semibold">Deskripsi Produk</label>
                <textarea name="description" 
                          rows="5"
                          class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500"
                          placeholder="Deskripsi lengkap tentang produk...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition font-semibold">
                    Simpan Produk
                </button>
                <a href="{{ route('merchandise.index') }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    // Image preview
    document.querySelector('input[name="image"]').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
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
    
    // Update normal price and stock display when price/stock changes
    document.querySelector('input[name="price"]').addEventListener('change', function(e) {
        const price = parseInt(e.target.value) || 0;
        document.getElementById('normalPriceDisplay').innerText = 'Rp ' + price.toLocaleString('id-ID');
    });
    
    document.querySelector('input[name="stock"]').addEventListener('change', function(e) {
        const stock = parseInt(e.target.value) || 0;
        document.getElementById('normalStockDisplay').innerText = stock + ' pcs';
    });
    
    // Trigger on load
    document.addEventListener('DOMContentLoaded', function() {
        const price = parseInt(document.querySelector('input[name="price"]').value) || 0;
        const stock = parseInt(document.querySelector('input[name="stock"]').value) || 0;
        document.getElementById('normalPriceDisplay').innerText = 'Rp ' + price.toLocaleString('id-ID');
        document.getElementById('normalStockDisplay').innerText = stock + ' pcs';
    });
</script>
@endsection