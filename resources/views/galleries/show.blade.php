@extends('layouts.app')

@section('title', 'Gallery Detail - ' . ($eventGallery->event->title ?? 'Event Gallery'))
@section('page-title', 'Gallery Detail')
@section('page-description', 'View all gallery images for ' . ($eventGallery->event->title ?? 'Event'))

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <a href="{{ route('galleries.index') }}" class="text-gray-400 hover:text-white transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Galleries
        </a>
        
        <div class="flex gap-2">
            <a href="{{ route('galleries.edit', $eventGallery) }}" 
               class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Gallery
            </a>
            <form action="{{ route('galleries.destroy', $eventGallery) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus gallery ini? Semua gambar akan terhapus.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete Gallery
                </button>
            </form>
        </div>
    </div>
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

<!-- Gallery Info Card -->
<div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6 mb-6">
    <div class="flex flex-wrap gap-6">
        <div class="flex-1">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-blue-500 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-white">{{ $eventGallery->event->title ?? 'Event Tidak Ditemukan' }}</h2>
                    <p class="text-gray-400">Event Gallery Documentation</p>
                </div>
            </div>
            
            @if($eventGallery->event)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 pt-4 border-t border-white/10">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <div>
                        <p class="text-gray-400 text-xs">Event Date</p>
                        <p class="text-white text-sm">{{ $eventGallery->event->start_date->format('d M Y') }} - {{ $eventGallery->event->end_date->format('d M Y') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <div>
                        <p class="text-gray-400 text-xs">Location</p>
                        <p class="text-white text-sm">{{ Str::limit($eventGallery->event->location, 50) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l5 5a2 2 0 01.586 1.414V19a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                    </svg>
                    <div>
                        <p class="text-gray-400 text-xs">Category</p>
                        <p class="text-white text-sm">{{ $eventGallery->event->category->name ?? 'Uncategorized' }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
        
        <div class="md:text-right">
            <div class="bg-white/5 rounded-lg p-3">
                <div class="flex items-center gap-2 md:justify-end mb-2">
                    <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-gray-300 text-sm">Uploaded by: <span class="text-white font-semibold">{{ $eventGallery->uploader->name ?? 'Unknown' }}</span></span>
                </div>
                <div class="flex items-center gap-2 md:justify-end mb-2">
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-gray-300 text-sm">{{ $eventGallery->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="flex items-center gap-2 md:justify-end">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-gray-300 text-sm">{{ count($images) }} Total Images</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gallery Images Section -->
<div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
    <div class="flex justify-between items-center mb-6 flex-wrap gap-3">
        <h3 class="text-xl font-bold text-white flex items-center gap-2">
            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Gallery Images
            <span class="text-sm text-gray-400 font-normal">({{ count($images) }} images)</span>
        </h3>
    </div>
    
    @if(count($images) > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($images as $index => $image)
                <div class="group relative aspect-square overflow-hidden rounded-lg bg-white/5 cursor-pointer hover:shadow-xl transition-all duration-300"
                     onclick="openLightbox({{ $index }})">
                    <img src="{{ Storage::url($image) }}" 
                         alt="Gallery image {{ $index + 1 }}"
                         class="w-full h-full object-cover transition transform group-hover:scale-110 duration-500">
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-all duration-300 flex flex-col justify-end p-3">
                        <div class="flex justify-between items-center">
                            <span class="text-white text-xs bg-black/50 px-2 py-1 rounded">Image {{ $index + 1 }}</span>
                            <div class="flex gap-2">
                                <button onclick="event.stopPropagation(); openLightbox({{ $index }})" 
                                        class="bg-blue-500/80 hover:bg-blue-600 rounded-full p-1.5 transition">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </button>
                                <form action="{{ route('galleries.delete-image', ['eventGallery' => $eventGallery->id, 'imageIndex' => $index]) }}" 
                                      method="POST" 
                                      class="inline"
                                      onsubmit="event.stopPropagation(); return confirm('Hapus gambar ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500/80 hover:bg-red-600 rounded-full p-1.5 transition">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="absolute bottom-2 left-2 bg-black/60 text-white text-xs px-2 py-1 rounded">
                        {{ $index + 1 }}/{{ count($images) }}
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-6 text-center">
            <p class="text-gray-400 text-sm">Showing all {{ count($images) }} images</p>
        </div>
    @else
        <div class="text-center py-12">
            <svg class="w-24 h-24 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-400 text-lg mb-4">Belum ada gambar dalam gallery ini</p>
            <a href="{{ route('galleries.edit', $eventGallery) }}" class="text-green-400 hover:text-green-300 inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Upload gambar sekarang
            </a>
        </div>
    @endif
</div>

<!-- Lightbox Modal -->
<div id="lightbox" class="fixed inset-0 bg-black/95 z-50 hidden items-center justify-center" onclick="closeLightbox()">
    <div class="relative max-w-7xl w-full mx-4" onclick="event.stopPropagation()">
        <img id="lightboxImage" src="" class="w-full h-auto rounded-lg max-h-[85vh] object-contain">
        
        <button id="prevBtn" onclick="navigateLightbox(-1)"
                class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white rounded-full p-3 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        
        <button id="nextBtn" onclick="navigateLightbox(1)"
                class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black/50 hover:bg-black/70 text-white rounded-full p-3 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
        
        <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white bg-black/50 hover:bg-black/70 rounded-full p-2 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        
        <div id="lightboxCounter" class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black/60 text-white px-3 py-1 rounded-full text-sm">
            1 / 1
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentImages = @json($images);
    let currentImageUrls = currentImages.map(img => "{{ Storage::url('') }}" + img);
    let currentIndex = 0;
    
    function openLightbox(index) {
        currentIndex = index;
        const modal = document.getElementById('lightbox');
        const modalImage = document.getElementById('lightboxImage');
        const counter = document.getElementById('lightboxCounter');
        
        modalImage.src = currentImageUrls[currentIndex];
        counter.textContent = `${currentIndex + 1} / ${currentImages.length}`;
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        
        updateNavigationButtons();
    }
    
    function closeLightbox() {
        const modal = document.getElementById('lightbox');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }
    
    function navigateLightbox(direction) {
        currentIndex += direction;
        
        if (currentIndex < 0) {
            currentIndex = currentImages.length - 1;
        }
        if (currentIndex >= currentImages.length) {
            currentIndex = 0;
        }
        
        const modalImage = document.getElementById('lightboxImage');
        const counter = document.getElementById('lightboxCounter');
        
        modalImage.src = currentImageUrls[currentIndex];
        counter.textContent = `${currentIndex + 1} / ${currentImages.length}`;
        
        updateNavigationButtons();
    }
    
    function updateNavigationButtons() {
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        
        if (currentImages.length <= 1) {
            prevBtn.style.opacity = '0.3';
            nextBtn.style.opacity = '0.3';
        } else {
            prevBtn.style.opacity = '1';
            nextBtn.style.opacity = '1';
        }
    }
    
    document.addEventListener('keydown', function(e) {
        const lightbox = document.getElementById('lightbox');
        if (lightbox.classList.contains('flex')) {
            if (e.key === 'Escape') {
                closeLightbox();
            } else if (e.key === 'ArrowLeft') {
                navigateLightbox(-1);
            } else if (e.key === 'ArrowRight') {
                navigateLightbox(1);
            }
        }
    });
</script>
@endpush
@endsection