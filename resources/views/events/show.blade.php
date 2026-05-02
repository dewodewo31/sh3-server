@extends('layouts.app')

@section('title', $event->title)
@section('page-title', 'Event Details')
@section('page-description', 'Complete information about the event')

@section('content')
<div class="mb-6">
    <a href="{{ route('events.index') }}" class="text-gray-400 hover:text-white transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Kembali ke Daftar Event
    </a>

</div>

<!-- Event Header -->
<div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 overflow-hidden mb-6">
    @if($event->image)
    <div class="relative h-48 md:h-72">
        <img src="{{ Storage::url($event->image) }}"
            alt="{{ $event->title }}"
            class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950 to-transparent"></div>
    </div>
    @else
    <div class="h-64 md:h-96 bg-gradient-to-br from-green-500/20 to-blue-500/20 flex items-center justify-center">
        <svg class="w-32 h-32 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
    </div>
    @endif

    <div class="p-6 -mt-20 relative z-10">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="flex-1">
                <!-- Status Badge -->
                <div class="mb-3">
                    @if($event->status == 'upcoming')
                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Akan Datang
                    </span>
                    @elseif($event->status == 'ongoing')
                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-green-500/20 text-green-300 rounded-full text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Sedang Berlangsung
                    </span>
                    @else
                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-gray-500/20 text-gray-300 rounded-full text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Selesai
                    </span>
                    @endif
                </div>

                <h1 class="text-3xl md:text-4xl font-bold text-white mb-3">{{ $event->title }}</h1>

                <div class="flex flex-wrap gap-4 text-sm text-gray-300">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                        </svg>
                        <span>{{ $event->category->name ?? 'Uncategorized' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>By: {{ $event->creator->name ?? 'Unknown' }}</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2">
                <a href="{{ route('events.edit', $event) }}"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Event
                </a>
                <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        onclick="return confirm('Yakin ingin menghapus event {{ $event->title }}?')"
                        class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus
                    </button>
                </form>
                <!-- Tambahkan di action buttons bersama Edit dan Delete -->
                <a href="{{ route('events.export-brochure', $event) }}"
                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export Brochure PDF
                </a>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content (Left) -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Description -->
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" />
                </svg>
                Deskripsi Event
            </h2>
            <div class="text-gray-300 leading-relaxed whitespace-pre-wrap">
                {{ $event->description }}
            </div>
        </div>

        <!-- Key Points -->
        @if($event->key_point && count($event->key_point) > 0)
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Key Points
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($event->key_point as $point)
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-green-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span class="text-gray-300">{{ $point }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Event Galleries -->
        @if($event->galleries && $event->galleries->count() > 0)
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Gallery Event
            </h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($event->galleries as $gallery)
                @if($gallery->image && is_array($gallery->image))
                @foreach($gallery->image as $image)
                <div class="relative group cursor-pointer" onclick="openGalleryModal('{{ Storage::url($image) }}')">
                    <img src="{{ Storage::url($image) }}"
                        class="w-full h-40 object-cover rounded-lg transition group-hover:scale-105">
                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                        </svg>
                    </div>
                </div>
                @endforeach
                @endif
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar (Right) -->
    <div class="space-y-6">
        <!-- Event Info Card -->
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6 sticky top-6">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Informasi Event
            </h3>

            <div class="space-y-4">
                <!-- Date -->
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <div>
                        <p class="text-gray-400 text-sm">Tanggal Event</p>
                        <p class="text-white font-semibold">
                            {{ $event->start_date->format('d F Y') }}
                        </p>
                        <p class="text-sm text-gray-400">
                            {{ $event->start_date->format('H:i') }} - {{ $event->end_date->format('H:i') }}
                        </p>
                    </div>
                </div>

                <!-- Duration -->
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-gray-400 text-sm">Durasi</p>
                        <p class="text-white font-semibold">
                            {{ $event->start_date->diffInDays($event->end_date) + 1 }} Hari
                        </p>
                    </div>
                </div>

                <!-- Location -->
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <div>
                        <p class="text-gray-400 text-sm">Lokasi</p>
                        <p class="text-white font-semibold">{{ $event->location }}</p>
                    </div>
                </div>

                <!-- Price -->
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-gray-400 text-sm">Harga Tiket</p>
                        @if($event->price > 0)
                        <p class="text-2xl font-bold text-green-400">
                            Rp {{ number_format($event->price, 0, ',', '.') }}
                        </p>
                        @else
                        <p class="text-2xl font-bold text-green-400">GRATIS</p>
                        @endif
                    </div>
                </div>

                <!-- Quota -->
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <div>
                        <p class="text-gray-400 text-sm">Kuota Peserta</p>
                        <p class="text-white font-semibold">{{ $event->quota }} Orang</p>
                        @php
                        $ordersCount = $event->orders->count();
                        $remainingQuota = $event->quota - $ordersCount;
                        @endphp
                        <div class="mt-2">
                            <div class="flex justify-between text-xs text-gray-400 mb-1">
                                <span>Terisi: {{ $ordersCount }}</span>
                                <span>Sisa: {{ $remainingQuota }}</span>
                            </div>
                            <div class="w-full bg-white/10 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: {{ ($ordersCount / $event->quota) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Gallery Modal -->
<div id="galleryModal" class="fixed inset-0 bg-black/90 z-50 hidden items-center justify-center p-4" onclick="closeGalleryModal()">
    <div class="relative max-w-4xl w-full" onclick="event.stopPropagation()">
        <img id="modalImage" src="" class="w-full h-auto rounded-lg">
        <button onclick="closeGalleryModal()" class="absolute top-4 right-4 text-white bg-black/50 rounded-full p-2 hover:bg-black/70">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>

@push('scripts')
<script>
    function openGalleryModal(imageUrl) {
        const modal = document.getElementById('galleryModal');
        const modalImage = document.getElementById('modalImage');
        modalImage.src = imageUrl;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeGalleryModal() {
        const modal = document.getElementById('galleryModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    function copyLink() {
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
            alert('Link berhasil disalin!');
        });
    }

    // Close modal with ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeGalleryModal();
        }
    });
</script>
@endpush
@endsection