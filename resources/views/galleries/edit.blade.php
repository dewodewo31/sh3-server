@extends('layouts.app')

@section('title', 'Edit Gallery')
@section('page-title', 'Edit Gallery')
@section('page-description', 'Update gallery images')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('galleries.index') }}"
                class="text-gray-400 hover:text-white transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Gallery
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
                    <h2 class="text-2xl font-bold text-white">Edit Gallery</h2>
                    <p class="text-sm text-gray-400">Event: {{ $eventGallery->event->title ?? 'N/A' }}</p>
                </div>
            </div>

            <form action="{{ route('galleries.update', $eventGallery) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Current Images -->
                <div class="mb-6">
                    <label class="block text-gray-300 mb-2 font-semibold">Gambar Saat Ini:</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @php
                            $images = is_array($eventGallery->image)
                                ? $eventGallery->image
                                : json_decode($eventGallery->image, true);
                        @endphp

                        @forelse($images as $index => $image)
                            <div id="image-{{ $index }}" class="relative group">
                                <img src="{{ Storage::url($image) }}" class="w-full h-32 object-cover rounded-lg">
                                <button type="button" onclick="deleteImage({{ $eventGallery->id }}, {{ $index }})"
                                    class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 rounded-full p-1 opacity-0 group-hover:opacity-100 transition">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                                <p class="text-xs text-gray-400 mt-1 truncate">Image {{ $index + 1 }}</p>
                            </div>
                        @empty
                            <div class="col-span-full text-center text-gray-400 py-8">
                                Belum ada gambar
                            </div>
                        @endforelse
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Klik tombol X pada gambar untuk menghapus</p>
                </div>

                <!-- Tabs for Add New Images -->
                <div class="mb-6">
                    <div class="flex border-b border-white/10 mb-4">
                        <button type="button" id="tabFile"
                            class="px-4 py-2 text-green-400 border-b-2 border-green-400 transition">Upload File</button>
                        <button type="button" id="tabDrive"
                            class="px-4 py-2 text-gray-400 hover:text-white transition">Google Drive Link</button>
                    </div>

                    <!-- Upload File Section -->
                    <div id="fileSection">
                        <div class="border-2 border-dashed border-white/20 rounded-lg p-6 text-center hover:border-green-500 transition cursor-pointer"
                            onclick="document.getElementById('newImages').click()">
                            <input type="file" name="images[]" id="newImages" class="hidden" multiple
                                accept="image/jpeg,image/jpg,image/png" onchange="previewNewImages(this)">

                            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            <p class="text-gray-400">Upload gambar baru untuk ditambahkan ke gallery</p>
                            <p class="text-xs text-gray-500 mt-1">Supported: JPG, JPEG, PNG (Max 2MB per file)</p>
                        </div>
                        @error('images')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Google Drive Link Section -->
                    <div id="driveSection" class="hidden">
                        <div class="border-2 border-white/20 rounded-lg p-6">
                            <label class="block text-gray-300 mb-2 font-semibold">Link Google Drive (pisahkan dengan
                                enter)</label>
                            <textarea name="google_drive_links" id="googleDriveLinks" rows="5"
                                class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 font-mono text-sm"
                                placeholder="https://drive.google.com/file/d/FILE_ID/view
https://drive.google.com/open?id=FILE_ID
https://drive.google.com/uc?id=FILE_ID"></textarea>
                            <div class="mt-3 flex justify-end">
                                <button type="button" id="openLinksBtn"
                                    class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-lg text-sm transition flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    Buka Semua Link
                                </button>
                            </div>
                            <p class="text-xs text-gray-400 mt-2">
                                💡 Tips:
                                <br>• Buka file di Google Drive, klik "Bagikan" dan set "Siapa saja dengan tautan dapat
                                melihat"
                                <br>• Salin link dan tempel di sini (satu link per baris)
                                <br>• Klik "Ekstrak File ID" untuk memvalidasi link
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Preview New Images -->
                <div id="previewArea" class="mb-6 hidden">
                    <label class="block text-gray-300 mb-2 font-semibold">Preview Gambar Baru:</label>
                    <div id="previewContainer" class="grid grid-cols-2 md:grid-cols-4 gap-4"></div>
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition font-semibold">
                        Update Gallery
                    </button>
                    <a href="{{ route('galleries.index') }}"
                        class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Image Form (Hidden) -->
    <form id="deleteImageForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script>
            let previewMap = null;
            let previewMarker = null;

            // Tab switching
            const tabFile = document.getElementById('tabFile');
            const tabDrive = document.getElementById('tabDrive');
            const fileSection = document.getElementById('fileSection');
            const driveSection = document.getElementById('driveSection');

            tabFile.addEventListener('click', function() {
                fileSection.classList.remove('hidden');
                driveSection.classList.add('hidden');
                tabFile.classList.add('text-green-400', 'border-green-400');
                tabFile.classList.remove('text-gray-400');
                tabDrive.classList.remove('text-green-400', 'border-green-400');
                tabDrive.classList.add('text-gray-400');
            });

            tabDrive.addEventListener('click', function() {
                fileSection.classList.add('hidden');
                driveSection.classList.remove('hidden');
                tabDrive.classList.add('text-green-400', 'border-green-400');
                tabDrive.classList.remove('text-gray-400');
                tabFile.classList.remove('text-green-400', 'border-green-400');
                tabFile.classList.add('text-gray-400');
            });

            // Fungsi untuk mengekstrak File ID dari URL Google Drive
            function extractFileId(url) {
                const patterns = [
                    /\/file\/d\/([a-zA-Z0-9_-]+)/,
                    /id=([a-zA-Z0-9_-]+)/,
                    /\/uc\?id=([a-zA-Z0-9_-]+)/,
                    /\/d\/([a-zA-Z0-9_-]+)/
                ];

                for (const pattern of patterns) {
                    const match = url.match(pattern);
                    if (match) {
                        return match[1];
                    }
                }
                return null;
            }

            // Ekstrak semua File ID dari textarea
            document.getElementById('extractBtn')?.addEventListener('click', function() {
                const textarea = document.getElementById('googleDriveLinks');
                let lines = textarea.value.split('\n');
                let extractedLines = [];

                for (let line of lines) {
                    let trimmedLine = line.trim();
                    if (trimmedLine) {
                        let fileId = extractFileId(trimmedLine);
                        if (fileId) {
                            extractedLines.push(fileId);
                        } else {
                            extractedLines.push(trimmedLine);
                        }
                    }
                }

                textarea.value = extractedLines.join('\n');
                alert('File ID berhasil diekstrak!');
            });

            function deleteImage(galleryId, imageIndex) {
                if (confirm('Yakin ingin menghapus gambar ini?')) {
                    const form = document.getElementById('deleteImageForm');
                    form.action = `/galleries/${galleryId}/delete-image/${imageIndex}`;
                    form.submit();
                }
            }

            function previewNewImages(input) {
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
