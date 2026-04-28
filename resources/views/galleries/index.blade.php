@extends('layouts.app')

@section('title', 'Event Gallery Management')
@section('page-title', 'Event Galleries')
@section('page-description', 'Manage all event gallery images')

@section('stats')
    <div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-400 text-sm">Total Galleries</p>
        </div>
        <h3 class="text-2xl font-bold text-green-400">{{ $galleries->count() }}</h3>
    </div>

    <div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <p class="text-gray-400 text-sm">Total Events</p>
        </div>
        <h3 class="text-2xl font-bold text-blue-400">{{ $galleries->unique('event_id')->count() }}</h3>
    </div>

    <div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-400 text-sm">Total Images</p>
        </div>
        <h3 class="text-2xl font-bold text-purple-400">
            {{ $galleries->sum(function($gallery) { return count($gallery->image); }) }}
        </h3>
    </div>
@endsection

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-white">Gallery Management</h2>
    <a href="{{ route('galleries.create') }}" 
       class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Gallery
    </a>
</div>

@if(session('success'))
    <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-4">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-4">
        {{ session('error') }}
    </div>
@endif

<!-- Galleries Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    @forelse($galleries as $gallery)
    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 overflow-hidden hover:border-green-500/50 transition-all duration-300 group">
        <!-- Gallery Header -->
        <div class="p-4 border-b border-white/10 bg-white/5">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <h3 class="font-semibold text-white">{{ $gallery->event->title ?? 'Event Tidak Ditemukan' }}</h3>
                    </div>
                    <p class="text-xs text-gray-400">
                        Uploaded by: {{ $gallery->uploader->name ?? 'Unknown' }} | 
                        {{ $gallery->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('galleries.edit', $gallery) }}" 
                       class="p-2 bg-yellow-500/20 hover:bg-yellow-500/30 rounded-lg text-yellow-400 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                    <form action="{{ route('galleries.destroy', $gallery) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Yakin ingin menghapus gallery ini? Semua gambar akan terhapus.')"
                                class="p-2 bg-red-500/20 hover:bg-red-500/30 rounded-lg text-red-400 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Gallery Images -->
        <div class="p-4">
            @php
                $images = is_array($gallery->image) ? $gallery->image : json_decode($gallery->image, true);
                $imageCount = count($images);
            @endphp
            
            @if($imageCount > 0)
                <div class="grid grid-cols-3 gap-2">
                    @foreach(array_slice($images, 0, 6) as $index => $image)
                        <div class="relative group/image aspect-square cursor-pointer" onclick="openImageModal('{{ Storage::url($image) }}')">
                            <img src="{{ Storage::url($image) }}" 
                                 class="w-full h-full object-cover rounded-lg transition group-hover/image:scale-105"
                                 alt="Gallery image">
                            @if($index === 5 && $imageCount > 6)
                                <div class="absolute inset-0 bg-black/70 rounded-lg flex items-center justify-center">
                                    <span class="text-white font-bold text-xl">+{{ $imageCount - 6 }}</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-gray-400 mt-3 text-center">{{ $imageCount }} gambar</p>
            @else
                <div class="text-center py-8 text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm">Tidak ada gambar</p>
                </div>
            @endif
        </div>
        
        <!-- Gallery Footer with Actions -->
        <div class="p-4 border-t border-white/10 bg-white/5">
            <div class="flex justify-between items-center text-sm">
                <div class="flex gap-3">
                    <button onclick="openGalleryDetail('{{ $gallery->id }}')" 
                            class="text-blue-400 hover:text-blue-300 transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Lihat Semua
                    </button>
                </div>
                <div class="text-gray-400 text-xs">
                    ID: #{{ $gallery->id }}
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full">
        <div class="text-center py-12">
            <svg class="w-24 h-24 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-400 text-lg mb-4">Belum ada gallery</p>
            <a href="{{ route('galleries.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Gallery Pertama
            </a>
        </div>
    </div>
    @endforelse
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black/90 z-50 hidden items-center justify-center p-4" onclick="closeImageModal()">
    <div class="relative max-w-4xl w-full" onclick="event.stopPropagation()">
        <img id="modalImage" src="" class="w-full h-auto rounded-lg">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white bg-black/50 rounded-full p-2 hover:bg-black/70">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>

@push('scripts')
<script>
    let allImages = [];
    let currentImageIndex = 0;
    
    function openImageModal(imageUrl) {
        const modal = document.getElementById('imageModal');
        const img = document.getElementById('modalImage');
        img.src = imageUrl;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    
    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }
    
    function openGalleryDetail(galleryId) {
        // Optional: Navigate to gallery detail page
        window.location.href = `/galleries/${galleryId}`;
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
        }
    });
</script>
@endpush
@endsection