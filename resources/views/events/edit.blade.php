@extends('layouts.app')

@section('title', 'Edit Event - ' . $event->title)
@section('page-title', 'Edit Event')
@section('page-description', 'Update event information')

@section('content')
    <div class="mb-6">
        <a href="{{ route('events.index') }}" class="text-gray-400 hover:text-white transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar Event
        </a>
    </div>

    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-8">
        <div class="flex items-center gap-3 mb-6">
            <div
                class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white">Edit Event</h2>
                <p class="text-sm text-gray-400">Update informasi event: {{ $event->title }}</p>
            </div>
        </div>

        <form action="{{ route('events.update', $event) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Judul Event *</label>
                        <input type="text" name="title" value="{{ old('title', $event->title) }}"
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 @error('title') border-red-500 @enderror"
                            placeholder="Masukkan judul event">
                        @error('title')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Kategori *</label>
                        <select name="category_id"
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 @error('category_id') border-red-500 @enderror">
                            <option value="" class="text-black">Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $event->category_id) == $category->id ? 'selected' : '' }}
                                    class="text-black">
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
                        <label class="block text-gray-300 mb-2 font-semibold">Lokasi *</label>
                        <input type="text" name="location" value="{{ old('location', $event->location) }}"
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 @error('location') border-red-500 @enderror"
                            placeholder="Masukkan lokasi event">
                        @error('location')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Location -->
                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Nama Lokasi *</label>
                        <div class="flex gap-2">
                            <input type="text" name="location" id="location_name"
                                value="{{ old('location', $event->location) }}"
                                class="flex-1 bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500">
                            <button type="button" onclick="getCoordinatesFromAddress()"
                                class="bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg transition">
                                Ambil Koordinat
                            </button>
                        </div>
                        @error('location')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Koordinat -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-300 mb-2 font-semibold">Latitude</label>
                            <input type="text" name="latitude" id="latitude"
                                value="{{ old('latitude', $event->latitude) }}"
                                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500"
                                placeholder="-6.2088">
                        </div>
                        <div>
                            <label class="block text-gray-300 mb-2 font-semibold">Longitude</label>
                            <input type="text" name="longitude" id="longitude"
                                value="{{ old('longitude', $event->longitude) }}"
                                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500"
                                placeholder="106.8456">
                        </div>
                    </div>

                    <!-- Preview Map -->
                    <div id="mapPreview" class="{{ $event->latitude ? '' : 'hidden' }}">
                        <label class="block text-gray-300 mb-2 font-semibold">Preview Lokasi:</label>
                        <div id="previewMap" style="height: 250px; border-radius: 0.5rem; z-index: 1;"></div>
                    </div>

                    <!-- Key Points -->
                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Key Points</label>
                        <div id="key-points-container" class="space-y-2">
                            @if ($event->key_point && is_array($event->key_point))
                                @foreach ($event->key_point as $index => $keyPoint)
                                    <div class="flex gap-2 key-point-item">
                                        <input type="text" name="key_point[]" value="{{ $keyPoint }}"
                                            class="flex-1 bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500"
                                            placeholder="Contoh: Akses gratis parkir">
                                        <button type="button" onclick="removeKeyPoint(this)"
                                            class="bg-red-500 hover:bg-red-600 px-3 rounded-lg transition">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" onclick="addKeyPoint()"
                            class="mt-2 text-green-400 hover:text-green-300 text-sm flex items-center gap-1 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Key Point
                        </button>
                        <p class="text-xs text-gray-400 mt-1">Point-point penting tentang event</p>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Date Range -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-300 mb-2 font-semibold">Tanggal Mulai *</label>
                            <input type="datetime-local" name="start_date"
                                value="{{ old('start_date', $event->start_date ? $event->start_date->format('Y-m-d\TH:i') : '') }}"
                                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 @error('start_date') border-red-500 @enderror">
                            @error('start_date')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2 font-semibold">Tanggal Selesai *</label>
                            <input type="datetime-local" name="end_date"
                                value="{{ old('end_date', $event->end_date ? $event->end_date->format('Y-m-d\TH:i') : '') }}"
                                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 @error('end_date') border-red-500 @enderror">
                            @error('end_date')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Price & Quota -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-300 mb-2 font-semibold">Harga</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">Rp</span>
                                <input type="number" name="price" value="{{ old('price', $event->price) }}"
                                    step="1000"
                                    class="w-full bg-white/5 border border-white/10 rounded-lg pl-8 pr-4 py-3 text-white focus:outline-none focus:border-green-500 @error('price') border-red-500 @enderror"
                                    placeholder="0">
                            </div>
                            <p class="text-xs text-gray-400 mt-1">0 = Event Gratis</p>
                            @error('price')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2 font-semibold">Kuota *</label>
                            <input type="number" name="quota" value="{{ old('quota', $event->quota) }}"
                                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 @error('quota') border-red-500 @enderror"
                                placeholder="Jumlah peserta">
                            @error('quota')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Image -->
                    <div>
                        <label class="block text-gray-300 mb-2 font-semibold">Gambar Event</label>

                        <!-- Current Image Preview -->
                        @if ($event->image)
                            <div class="mb-3">
                                <p class="text-xs text-gray-400 mb-2">Gambar Saat Ini:</p>
                                <div class="relative inline-block">
                                    <img src="{{ Storage::url($event->image) }}"
                                        class="w-32 h-32 rounded-lg object-cover border border-white/10">
                                    <button type="button" onclick="removeImage()"
                                        class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 rounded-full p-1 transition">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <input type="hidden" name="remove_image" id="remove_image" value="0">
                            </div>
                        @endif

                        <!-- New Image Upload -->
                        <div class="border-2 border-dashed border-white/20 rounded-lg p-4 text-center hover:border-green-500 transition cursor-pointer"
                            onclick="document.getElementById('image').click()">
                            <input type="file" name="image" id="image" class="hidden"
                                accept="image/jpeg,image/jpg,image/png" onchange="previewImage(this)">

                            <svg class="w-10 h-10 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="text-gray-400 text-sm">Klik untuk ganti gambar</p>
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG. Max 2MB</p>
                        </div>

                        <!-- New Image Preview -->
                        <div id="newImagePreview" class="mt-3 hidden">
                            <p class="text-xs text-gray-400 mb-2">Preview Gambar Baru:</p>
                            <img id="preview" class="w-32 h-32 rounded-lg object-cover border border-green-500">
                        </div>

                        @error('image')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <label class="block text-gray-300 mb-2 font-semibold">Deskripsi *</label>
                <textarea name="description" rows="8"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 @error('description') border-red-500 @enderror"
                    placeholder="Deskripsi lengkap tentang event">{{ old('description', $event->description) }}</textarea>
                @error('description')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Event Info Card -->
            <div class="bg-white/5 rounded-lg p-4 mt-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-gray-400">Event ID</p>
                        <p class="text-white font-semibold">#{{ $event->id }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Created By</p>
                        <p class="text-white">{{ $event->creator->name ?? 'Unknown' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Created At</p>
                        <p class="text-white">{{ $event->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Last Update</p>
                        <p class="text-white">{{ $event->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Status info if event has orders -->
            @if ($event->orders()->count() > 0)
                <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-4 mt-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p class="text-yellow-300 text-sm">Event ini sudah memiliki {{ $event->orders()->count() }}
                            pesanan. Perubahan pada event akan mempengaruhi data yang sudah ada.</p>
                    </div>
                </div>
            @endif

            <!-- Submit Buttons -->
            <div class="flex gap-3 mt-6">
                <button type="submit"
                    class="flex-1 bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition font-semibold">
                    Update Event
                </button>
                <a href="{{ route('events.index') }}"
                    class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition text-center">
                    Batal
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            let previewMap = null;
            let previewMarker = null;

            // Add Key Point
            function addKeyPoint() {
                const container = document.getElementById('key-points-container');
                const div = document.createElement('div');
                div.className = 'flex gap-2 key-point-item';
                div.innerHTML = `
            <input type="text" 
                   name="key_point[]" 
                   class="flex-1 bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-green-500"
                   placeholder="Key point lainnya">
            <button type="button" onclick="removeKeyPoint(this)" class="bg-red-500 hover:bg-red-600 px-3 rounded-lg transition">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/>
                </svg>
            </button>
        `;
                container.appendChild(div);
            }

            // Remove Key Point
            function removeKeyPoint(btn) {
                btn.closest('.key-point-item').remove();
            }

            // Remove current image
            function removeImage() {
                if (confirm('Hapus gambar event ini?')) {
                    document.getElementById('remove_image').value = '1';
                    document.querySelector('#image').closest('.border-2').previousElementSibling?.remove();
                    location.reload();
                }
            }

            // Preview new image
            function previewImage(input) {
                const previewArea = document.getElementById('newImagePreview');
                const preview = document.getElementById('preview');

                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        previewArea.classList.remove('hidden');
                    }
                    reader.readAsDataURL(input.files[0]);
                } else {
                    previewArea.classList.add('hidden');
                }
            }

            // ========== FUNGSI UNTUK MAPS ==========

            // Fungsi untuk mengambil koordinat dari alamat (OpenStreetMap Nominatim - GRATIS)
            async function getCoordinatesFromAddress() {
                const address = document.getElementById('location_name').value;
                if (!address) {
                    alert('Masukkan alamat terlebih dahulu');
                    return;
                }

                const button = event.target;
                const originalText = button.innerText;
                button.innerText = 'Loading...';
                button.disabled = true;

                try {
                    const response = await fetch(
                        `https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(address)}&format=json&limit=1`, {
                            headers: {
                                'User-Agent': 'SH3-Event-App/1.0'
                            }
                        });
                    const data = await response.json();

                    if (data && data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lng = parseFloat(data[0].lon);

                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lng;

                        // Tampilkan preview map
                        showPreviewMap(lat, lng, address);

                        alert(`Koordinat ditemukan!\nLatitude: ${lat}\nLongitude: ${lng}`);
                    } else {
                        alert('Lokasi tidak ditemukan. Coba gunakan alamat yang lebih spesifik.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Gagal mengambil koordinat. Periksa koneksi internet Anda.');
                } finally {
                    button.innerText = originalText;
                    button.disabled = false;
                }
            }

            // Fungsi untuk menampilkan preview map
            function showPreviewMap(lat, lng, address) {
                const previewDiv = document.getElementById('mapPreview');
                previewDiv.classList.remove('hidden');

                if (previewMap) {
                    previewMap.remove();
                }

                previewMap = L.map('previewMap').setView([lat, lng], 15);

                L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    subdomains: 'abcd',
                    maxZoom: 19,
                    minZoom: 3
                }).addTo(previewMap);

                previewMarker = L.marker([lat, lng]).addTo(previewMap);
                previewMarker.bindPopup(`<b>${address}</b>`).openPopup();
            }

            // Inisialisasi preview map jika sudah ada koordinat
            const existingLat = document.getElementById('latitude').value;
            const existingLng = document.getElementById('longitude').value;
            const existingAddress = document.getElementById('location_name').value;

            if (existingLat && existingLng && existingLat !== '' && existingLng !== '') {
                showPreviewMap(parseFloat(existingLat), parseFloat(existingLng), existingAddress);
            }
        </script>
    @endpush
@endsection
