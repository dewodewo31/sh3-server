@extends('layouts.app')

@section('title', 'Event Management')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-white">Event Management</h2>
    <a href="{{ route('events.create') }}" 
       class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Event
    </a>
</div>

@if(session('success'))
    <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- Events Table -->
<div class="overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="border-b border-white/10">
                <th class="text-left py-3 px-4 text-gray-400 font-semibold">#</th>
                <th class="text-left py-3 px-4 text-gray-400 font-semibold">Event</th>
                <th class="text-left py-3 px-4 text-gray-400 font-semibold">Kategori</th>
                <th class="text-left py-3 px-4 text-gray-400 font-semibold">Tanggal</th>
                <th class="text-left py-3 px-4 text-gray-400 font-semibold">Status</th>
                <th class="text-left py-3 px-4 text-gray-400 font-semibold">Kuota</th>
                <th class="text-left py-3 px-4 text-gray-400 font-semibold">Harga</th>
                <th class="text-center py-3 px-4 text-gray-400 font-semibold">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($events as $event)
            <tr class="border-b border-white/10 hover:bg-white/5 transition">
                <td class="py-3 px-4">{{ $loop->iteration }}</td>
                <td class="py-3 px-4">
                    <div class="flex items-center gap-3">
                        @if($event->image)
                            <img src="{{ Storage::url($event->image) }}" 
                                 class="w-10 h-10 rounded-lg object-cover" alt="{{ $event->title }}">
                        @else
                            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-500 to-blue-500 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                        <div>
                            <p class="font-semibold">{{ $event->title }}</p>
                            <p class="text-xs text-gray-400">{{ Str::limit($event->description, 50) }}</p>
                        </div>
                    </div>
                </td>
                <td class="py-3 px-4">
                    <span class="px-2 py-1 bg-blue-500/20 text-blue-300 rounded-full text-xs">
                        {{ $event->category->name ?? 'Uncategorized' }}
                    </span>
                </td>
                <td class="py-3 px-4 text-sm">
                    <div>{{ $event->start_date->format('d M Y') }}</div>
                    <div class="text-xs text-gray-400">s/d {{ $event->end_date->format('d M Y') }}</div>
                </td>
                <td class="py-3 px-4">
                    @if($event->status == 'upcoming')
                        <span class="px-2 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-xs">Akan Datang</span>
                    @elseif($event->status == 'ongoing')
                        <span class="px-2 py-1 bg-green-500/20 text-green-300 rounded-full text-xs">Berlangsung</span>
                    @else
                        <span class="px-2 py-1 bg-gray-500/20 text-gray-300 rounded-full text-xs">Selesai</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-center">
                    {{ $event->quota }}
                </td>
                <td class="py-3 px-4">
                    @if($event->price > 0)
                        Rp {{ number_format($event->price, 0, ',', '.') }}
                    @else
                        <span class="text-green-400">GRATIS</span>
                    @endif
                </td>
                <td class="py-3 px-4">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('events.show', $event) }}" 
                           class="text-blue-400 hover:text-blue-300 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        <a href="{{ route('events.edit', $event) }}" 
                           class="text-yellow-400 hover:text-yellow-300 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                        <form action="{{ route('events.destroy', $event) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('Yakin ingin menghapus event ini?')"
                                    class="text-red-400 hover:text-red-300 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="py-8 text-center text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p>Belum ada event yang dibuat</p>
                    <a href="{{ route('events.create') }}" class="text-green-400 hover:text-green-300 mt-2 inline-block">
                        Buat event sekarang
                    </a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($events->hasPages())
    <div class="mt-6">
        {{ $events->links() }}
    </div>
@endif
@endsection