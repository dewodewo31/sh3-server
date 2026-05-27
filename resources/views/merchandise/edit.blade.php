@extends('layouts.app')

@section('title', 'Edit Merchandise - ' . $merchandise->name)
@section('page-title', 'Edit Product')
@section('page-description', 'Update merchandise product information')

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
            <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white">Edit Produk</h2>
                <p class="text-sm text-gray-400">Update data merchandise: {{ $merchandise->name }}</p>
            </div>
        </div>

        <form action="{{ route('merchandise.update', $merchandise) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Nama Produk *</label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name', $merchandise->name) }}"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 @error('name') border-red-500 @enderror"
                               required>
                        @error('name')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Kategori *</label>
                        <select name="category" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500" required>
                            <option value="">Pilih Kategori</option>
                            <option value="clothing" {{ old('category', $merchandise->category) == 'clothing' ? 'selected' : '' }}>Clothing</option>
                            <option value="accessories" {{ old('category', $merchandise->category) == 'accessories' ? 'selected' : '' }}>Accessories</option>
                            <option value="collectibles" {{ old('category', $merchandise->category) == 'collectibles' ? 'selected' : '' }}>Collectibles</option>
                            <option value="others" {{ old('category', $merchandise->category) == 'others' ? 'selected' : '' }}>Others</option>
                        </select>
                        @error('category')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Harga (Rp) *</label>
                        <input type="number" 
                               name="price" 
                               value="{{ old('price', $merchandise->price) }}"
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
                               value="{{ old('stock', $merchandise->stock) }}"
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
                        
                        @if($merchandise->image)
                        <div class="mb-3">
                            <p class="text-xs text-gray-400 mb-2">Gambar Saat Ini:</p>
                            <img src="{{ Storage::url($merchandise->image) }}" class="w-32 h-32 rounded-lg object-cover border border-white/10">
                        </div>
                        @endif
                        
                        <input type="file" 
                               name="image" 
                               accept="image/*"
                               class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-green-500 file:text-white file:cursor-pointer hover:file:bg-green-600">
                        <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengganti gambar</p>
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
                                    <input type="checkbox" name="sizes[]" value="{{ $size }}" 
                                           {{ is_array($merchandise->sizes) && in_array($size, $merchandise->sizes) ? 'checked' : '' }}
                                           class="rounded bg-white/5 border-white/10">
                                    <span class="text-gray-300">{{ $size }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Warna (Color)</label>
                        <div class="flex flex-wrap gap-3">
                            @foreach($colors as $color)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="colors[]" value="{{ $color }}" 
                                           {{ is_array($merchandise->colors) && in_array($color, $merchandise->colors) ? 'checked' : '' }}
                                           class="rounded bg-white/5 border-white/10">
                                    <span class="text-gray-300">{{ $color }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Event Selection with Special Price and Stock -->
                    <div x-data="{ 
                        selectedEvent: '{{ $merchandise->events->first()->id ?? '' }}',
                        discountPrice: '{{ $merchandise->events->first()->pivot->discount_price ?? '' }}',
                        eventStock: '{{ $merchandise->events->first()->pivot->event_stock ?? '' }}'
                    }">
                        <label class="block text-gray-300 mb-2 font-semibold">Event (Opsional)</label>
                        <select name="event_id" x-model="selectedEvent" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500">
                            <option value="">-- Tanpa Event (Produk Umum) --</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" {{ old('event_id', $merchandise->events->first()->id ?? '') == $event->id ? 'selected' : '' }}>
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
                        <div x-show="selectedEvent" class="mt-4 space-y-3">
                            <div class="p-4 bg-blue-500/10 rounded-lg border border-blue-500/30">
                                <h4 class="text-sm font-semibold text-blue-300 mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Pengaturan Khusus Event
                                </h4>
                                
                                <!-- Harga Khusus -->
                                <div class="mb-3">
                                    <label class="block text-gray-300 text-sm mb-1">Harga Khusus Event (Opsional)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">Rp</span>
                                        <input type="number" 
                                               name="event_discount_price"
                                               x-model="discountPrice"
                                               class="w-full bg-black/30 border border-white/10 rounded-lg pl-10 pr-4 py-2 text-white focus:outline-none focus:border-green-500"
                                               placeholder="Kosongkan untuk harga normal ({{ number_format($merchandise->price, 0, ',', '.') }})">
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1">
                                        Harga normal: Rp {{ number_format($merchandise->price, 0, ',', '.') }}
                                    </p>
                                </div>
                                
                                <!-- Stok Khusus Event -->
                                <div>
                                    <label class="block text-gray-300 text-sm mb-1">Stok Khusus Event (Opsional)</label>
                                    <input type="number" 
                                           name="event_stock"
                                           x-model="eventStock"
                                           class="w-full bg-black/30 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500"
                                           placeholder="Kosongkan untuk stok normal ({{ $merchandise->stock }})">
                                    <p class="text-xs text-gray-400 mt-1">
                                        Stok normal: {{ $merchandise->stock }} pcs
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" 
                               {{ old('is_active', $merchandise->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 rounded bg-white/5 border-white/10">
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
                          placeholder="Deskripsi lengkap tentang produk...">{{ old('description', $merchandise->description) }}</textarea>
                @error('description')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Associated Events Card -->
            @if($merchandise->events->count() > 0)
            <div class="bg-white/5 rounded-lg p-4 mt-6">
                <h4 class="text-sm font-semibold text-gray-300 mb-3">Terhubung dengan Event:</h4>
                <div class="space-y-2">
                    @foreach($merchandise->events as $event)
                    <div class="flex justify-between items-center p-3 bg-white/5 rounded-lg">
                        <div>
                            <p class="text-white text-sm font-semibold">{{ $event->title }}</p>
                            <p class="text-xs text-gray-400">{{ $event->start_date->format('d M Y') }} - {{ $event->end_date->format('d M Y') }}</p>
                            @if($event->pivot->discount_price)
                                <p class="text-xs text-green-400 mt-1">
                                    Harga Event: Rp {{ number_format($event->pivot->discount_price, 0, ',', '.') }}
                                    <span class="text-gray-500 line-through ml-2">Rp {{ number_format($merchandise->price, 0, ',', '.') }}</span>
                                </p>
                            @else
                                <p class="text-xs text-gray-400 mt-1">Harga: Mengikuti harga normal</p>
                            @endif
                            @if($event->pivot->event_stock)
                                <p class="text-xs text-blue-400">Stok Event: {{ $event->pivot->event_stock }} pcs</p>
                            @else
                                <p class="text-xs text-gray-400">Stok: Mengikuti stok normal</p>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('events.show', $event) }}" class="text-green-400 hover:text-green-300 text-sm">
                                Lihat Event →
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Info Card -->
            <div class="bg-white/5 rounded-lg p-4 mt-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-gray-400">Product ID</p>
                        <p class="text-white font-semibold">#{{ $merchandise->id }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Total Sold</p>
                        <p class="text-green-400 font-semibold">{{ $merchandise->sold_count }} pcs</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Created At</p>
                        <p class="text-white">{{ $merchandise->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Last Update</p>
                        <p class="text-white">{{ $merchandise->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="flex-1 bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition font-semibold">
                    Update Produk
                </button>
                <a href="{{ route('merchandise.index') }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
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
</script>
@endsection