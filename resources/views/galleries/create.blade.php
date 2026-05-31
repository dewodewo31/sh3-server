@extends('layouts.app')

@section('title', 'Tambah Gallery')
@section('page-title', 'Create New Gallery')
@section('page-description', 'Add images to event gallery (upload file or Google Drive)')

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
                <p class="text-sm text-gray-400">Upload file atau gunakan link Google Drive</p>
            </div>
        </div>

        <form action="{{ route('galleries.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Pilih Event -->
            <div class="mb-6">
                <label class="block text-gray-300 mb-2 font-semibold">Pilih Event *</label>
                <select name="event_id" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500" required>
                    <option class="text-black" value="">-- Pilih Event --</option>
                    @foreach($events as $event)
                        <option class="text-black" value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>
                            {{ $event->title }} - {{ $event->start_date->format('d M Y') }}
                        </option>
                    @endforeach
                </select>
                @error('event_id')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tabs for Upload Method -->
            <div class="mb-6">
                <div class="flex border-b border-white/10 mb-4">
                    <button type="button" id="tabFile" class="px-4 py-2 text-green-400 border-b-2 border-green-400 transition">Upload File</button>
                    <button type="button" id="tabDrive" class="px-4 py-2 text-gray-400 hover:text-white transition">Google Drive Link</button>
                </div>

                <!-- Upload File Section -->
                <div id="fileSection">
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
                    </div>
                    @error('images')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @error('images.*')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

<!-- Google Drive Link Section -->
<div id="driveSection" class="hidden">
    <div class="border-2 border-white/20 rounded-lg p-6">
        <label class="block text-gray-300 mb-2 font-semibold">
            Link Google Drive (pisahkan dengan koma)
        </label>
        
        <textarea name="google_drive_links" 
                  id="googleDriveLinks"
                  rows="6"
                  class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 font-mono text-sm"
                  placeholder="https://drive.google.com/file/d/FILE_ID/view, https://drive.google.com/open?id=FILE_ID, https://drive.google.com/uc?id=FILE_ID"></textarea>
        
        <div class="flex flex-wrap gap-2 mt-3">
            <button type="button" id="formatLinksBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-lg text-sm transition flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Format Ulang Link
            </button>
            
            <button type="button" id="extractIdsBtn" class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded-lg text-sm transition flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Ekstrak File ID
            </button>
            
            <button type="button" id="openLinksBtn" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-lg text-sm transition flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Buka Semua Link
            </button>
            
            <button type="button" id="clearLinksBtn" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-lg text-sm transition flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Clear All
            </button>
        </div>
        
        <div id="driveLinksPreview" class="mt-4 hidden">
            <label class="block text-gray-300 mb-2 font-semibold text-sm">Preview Link:</label>
            <div id="linksContainer" class="space-y-2 max-h-40 overflow-y-auto"></div>
        </div>
        
        <p class="text-xs text-gray-400 mt-3">
            💡 Tips: 
            <br>• Pisahkan link dengan <span class="text-yellow-400 font-semibold">koma (,)</span> atau spasi
            <br>• Contoh: <code class="text-green-400">link1, link2, link3</code>
            <br>• Bisa juga dengan enter (setiap baris otomatis dipisah)
            <br>• Klik "Ekstrak File ID" untuk mengambil ID dari link
            <br>• Pastikan file di Google Drive sudah di-set "Siapa saja dengan tautan dapat melihat"
        </p>
    </div>
</div>
            </div>

            <!-- Preview Area untuk File Upload -->
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

<script>
    // Tab switching
    const tabFile = document.getElementById('tabFile');
    const tabDrive = document.getElementById('tabDrive');
    const fileSection = document.getElementById('fileSection');
    const driveSection = document.getElementById('driveSection');
    
    if (tabFile && tabDrive) {
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
    }
    
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
    
    // Fungsi untuk mendapatkan URL view dari File ID
    function getViewUrl(fileId) {
        return `https://drive.google.com/file/d/${fileId}/view`;
    }
    
    // Fungsi untuk memisahkan link (koma, spasi, atau enter)
    function splitLinks(input) {
        // Pisahkan dengan koma, spasi, atau new line
        let links = input.split(/[,\s\n]+/);
        // Filter empty strings dan trim
        return links.filter(link => link.trim().length > 0).map(link => link.trim());
    }
    
    // Update preview links
    function updateLinksPreview() {
        const textarea = document.getElementById('googleDriveLinks');
        const previewDiv = document.getElementById('driveLinksPreview');
        const linksContainer = document.getElementById('linksContainer');
        
        if (!textarea || !previewDiv || !linksContainer) return;
        
        let text = textarea.value;
        let links = splitLinks(text);
        let validLinks = [];
        
        linksContainer.innerHTML = '';
        
        for (let link of links) {
            let fileId = extractFileId(link);
            if (fileId) {
                validLinks.push({
                    original: link,
                    fileId: fileId,
                    viewUrl: getViewUrl(fileId)
                });
            }
        }
        
        if (validLinks.length > 0) {
            previewDiv.classList.remove('hidden');
            
            validLinks.forEach((link, index) => {
                const linkDiv = document.createElement('div');
                linkDiv.className = 'bg-white/5 rounded-lg p-2 flex justify-between items-center';
                linkDiv.innerHTML = `
                    <div class="flex-1">
                        <code class="text-green-400 text-xs font-mono">${link.fileId}</code>
                        <p class="text-xs text-gray-400 truncate">${link.original.substring(0, 60)}${link.original.length > 60 ? '...' : ''}</p>
                    </div>
                    <div class="flex gap-2 ml-2 flex-shrink-0">
                        <a href="${link.viewUrl}" target="_blank" class="text-blue-400 hover:text-blue-300" title="Buka di Google Drive">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                        <button type="button" onclick="copyToClipboard('${link.fileId}')" class="text-gray-400 hover:text-white" title="Copy File ID">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                `;
                linksContainer.appendChild(linkDiv);
            });
        } else {
            previewDiv.classList.add('hidden');
        }
    }
    
    // Format ulang link (gabungkan dengan koma)
    document.getElementById('formatLinksBtn')?.addEventListener('click', function() {
        const textarea = document.getElementById('googleDriveLinks');
        let text = textarea.value;
        let links = splitLinks(text);
        
        if (links.length > 0) {
            textarea.value = links.join(', ');
            updateLinksPreview();
        }
    });
    
    // Ekstrak semua File ID
    document.getElementById('extractIdsBtn')?.addEventListener('click', function() {
        const textarea = document.getElementById('googleDriveLinks');
        let text = textarea.value;
        let links = splitLinks(text);
        let extractedIds = [];
        
        for (let link of links) {
            let fileId = extractFileId(link);
            if (fileId) {
                extractedIds.push(fileId);
            } else {
                extractedIds.push(link);
            }
        }
        
        textarea.value = extractedIds.join(', ');
        updateLinksPreview();
        alert('File ID berhasil diekstrak!');
    });
    
    // Buka semua link Google Drive
    document.getElementById('openLinksBtn')?.addEventListener('click', function() {
        const textarea = document.getElementById('googleDriveLinks');
        let text = textarea.value;
        let links = splitLinks(text);
        let openedCount = 0;
        
        for (let link of links) {
            let fileId = extractFileId(link);
            if (fileId) {
                window.open(getViewUrl(fileId), '_blank');
                openedCount++;
            }
        }
        
        if (openedCount > 0) {
            alert(`${openedCount} link berhasil dibuka di tab baru!`);
        } else {
            alert('Tidak ada link Google Drive yang valid ditemukan.');
        }
    });
    
    // Clear all links
    document.getElementById('clearLinksBtn')?.addEventListener('click', function() {
        if (confirm('Yakin ingin menghapus semua link?')) {
            document.getElementById('googleDriveLinks').value = '';
            updateLinksPreview();
        }
    });
    
    // Copy to clipboard function
    window.copyToClipboard = function(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('File ID berhasil disalin: ' + text);
        });
    };
    
    // Update preview ketika textarea berubah
    const driveTextarea = document.getElementById('googleDriveLinks');
    if (driveTextarea) {
        driveTextarea.addEventListener('input', updateLinksPreview);
        driveTextarea.addEventListener('change', updateLinksPreview);
    }
    
    // Image preview for file upload
    function previewImages(input) {
        const previewArea = document.getElementById('previewArea');
        const previewContainer = document.getElementById('previewContainer');
        
        if (!previewArea || !previewContainer) return;
        
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
    
    // Inisialisasi preview links jika ada nilai awal
    if (driveTextarea && driveTextarea.value.trim()) {
        updateLinksPreview();
    }
</script>
@endsection