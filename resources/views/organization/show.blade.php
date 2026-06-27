@extends('layouts.app')

@section('title', 'Detail Jabatan - ' . $hierarchy->position_name)
@section('page-title', 'Detail Jabatan')
@section('page-description', 'Informasi lengkap tentang jabatan')

@section('content')
<div class="mb-6">
    <a href="{{ route('organization.index', ['year' => $hierarchy->year]) }}" class="text-gray-400 hover:text-white transition flex items-center gap-2 text-sm">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Struktur Organisasi
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Info -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Position Header -->
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-xs px-2 py-0.5 rounded-full level-badge-{{ $hierarchy->level }} font-medium">
                            Level {{ $hierarchy->level }}
                        </span>
                        @if($hierarchy->level_name)
                            <span class="text-xs text-gray-400">{{ $hierarchy->level_name }}</span>
                        @endif
                        @if(!$hierarchy->is_active)
                            <span class="text-xs px-2 py-0.5 bg-red-500/20 text-red-300 rounded-full">Nonaktif</span>
                        @endif
                    </div>
                    <h1 class="text-3xl font-bold text-white">{{ $hierarchy->position_name }}</h1>
                    @if($hierarchy->position_code)
                        <p class="text-sm text-gray-400 mt-1">Kode: <span class="font-mono text-green-400">{{ $hierarchy->position_code }}</span></p>
                    @endif
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('organization.edit', $hierarchy) }}" 
                       class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                    <a href="{{ route('organization.duplicate', $hierarchy) }}" 
                       onclick="return confirm('Duplikasi jabatan {{ $hierarchy->position_name }}?')"
                       class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Duplikasi
                    </a>
                </div>
            </div>
        </div>

        <!-- Description -->
        @if($hierarchy->description || $hierarchy->responsibilities)
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            @if($hierarchy->description)
                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-gray-300 mb-2">Deskripsi</h3>
                    <p class="text-gray-300 whitespace-pre-wrap">{{ $hierarchy->description }}</p>
                </div>
            @endif
            
            @if($hierarchy->responsibilities)
                <div>
                    <h3 class="text-sm font-semibold text-gray-300 mb-2">Tanggung Jawab</h3>
                    <div class="text-gray-300 whitespace-pre-wrap">{{ $hierarchy->responsibilities }}</div>
                </div>
            @endif
        </div>
        @endif

        <!-- Hierarchy Path -->
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <h3 class="text-sm font-semibold text-gray-300 mb-3">Jalur Hierarki</h3>
            <div class="flex items-center gap-2 text-sm flex-wrap">
                @php
                    $path = $hierarchy->path;
                    $parts = explode(' > ', $path);
                @endphp
                @foreach($parts as $index => $part)
                    <span class="{{ $index == count($parts) - 1 ? 'text-green-400 font-semibold' : 'text-gray-400' }}">
                        {{ $part }}
                    </span>
                    @if($index < count($parts) - 1)
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    @endif
                @endforeach
            </div>
            
            <div class="mt-3 grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-400">Tahun</span>
                    <p class="text-white font-semibold">{{ $hierarchy->year }}</p>
                </div>
                <div>
                    <span class="text-gray-400">Level</span>
                    <p class="text-white font-semibold">{{ $hierarchy->level }}</p>
                </div>
                <div>
                    <span class="text-gray-400">Urutan</span>
                    <p class="text-white font-semibold">{{ $hierarchy->sort_order ?? 0 }}</p>
                </div>
                <div>
                    <span class="text-gray-400">Status</span>
                    <p class="{{ $hierarchy->is_active ? 'text-green-400' : 'text-red-400' }} font-semibold">
                        {{ $hierarchy->is_active ? 'Aktif' : 'Nonaktif' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Children -->
        @if($hierarchy->children && $hierarchy->children->count() > 0)
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <h3 class="text-sm font-semibold text-gray-300 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Jabatan Bawahan ({{ $hierarchy->children->count() }})
            </h3>
            <div class="space-y-2">
                @foreach($hierarchy->children as $child)
                <div class="bg-white/5 rounded-lg p-3 hover:bg-white/10 transition flex justify-between items-center">
                    <div>
                        <span class="text-xs px-2 py-0.5 rounded-full level-badge-{{ $child->level }} font-medium">
                            Lv.{{ $child->level }}
                        </span>
                        <span class="text-white font-medium ml-2">{{ $child->position_name }}</span>
                        @if($child->level_name)
                            <span class="text-xs text-gray-400 ml-2">({{ $child->level_name }})</span>
                        @endif
                    </div>
                    <a href="{{ route('organization.show', $child) }}" class="text-blue-400 hover:text-blue-300 text-sm">
                        Detail →
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Position Holder -->
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6 sticky top-6">
            <h3 class="text-sm font-semibold text-gray-300 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Pemegang Jabatan
            </h3>
            
            @if($hierarchy->holders && $hierarchy->holders->count() > 0)
                @foreach($hierarchy->holders as $holder)
                <div class="bg-white/5 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($holder->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-white font-semibold">{{ $holder->display_name }}</p>
                            @if($holder->email)
                                <p class="text-xs text-gray-400">{{ $holder->email }}</p>
                            @endif
                            @if($holder->phone)
                                <p class="text-xs text-gray-400">{{ $holder->phone }}</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
                        @if($holder->member_since)
                            <div>
                                <span class="text-gray-400">Anggota Sejak</span>
                                <p class="text-white">{{ $holder->member_since }}</p>
                            </div>
                        @endif
                        @if($holder->period_text)
                            <div>
                                <span class="text-gray-400">Periode</span>
                                <p class="text-white">{{ $holder->period_text }}</p>
                            </div>
                        @endif
                    </div>
                    
                    @if($holder->bio)
                        <div class="mt-2 pt-2 border-t border-white/10">
                            <p class="text-xs text-gray-400">{{ $holder->bio }}</p>
                        </div>
                    @endif
                    
                    @if($holder->achievements)
                        <div class="mt-2">
                            <p class="text-xs text-gray-400">Prestasi:</p>
                            <p class="text-xs text-gray-300">{{ $holder->achievements }}</p>
                        </div>
                    @endif
                </div>
                @endforeach
            @else
                <div class="text-center py-6">
                    <svg class="w-12 h-12 mx-auto text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <p class="text-gray-400 text-sm">Belum ada pemegang</p>
                    <a href="{{ route('organization.edit', $hierarchy) }}" class="text-green-400 hover:text-green-300 text-sm mt-2 inline-block">
                        Tambah pemegang
                    </a>
                </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-4">
            <h4 class="text-sm font-semibold text-gray-300 mb-3">Aksi Cepat</h4>
            <div class="space-y-2">
                <a href="{{ route('organization.edit', $hierarchy) }}" 
                   class="w-full bg-yellow-500/20 hover:bg-yellow-500/30 text-yellow-300 px-4 py-2 rounded-lg transition text-sm text-center block">
                    Edit Jabatan
                </a>
                <a href="{{ route('organization.duplicate', $hierarchy) }}" 
                   onclick="return confirm('Duplikasi jabatan {{ $hierarchy->position_name }}?')"
                   class="w-full bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 px-4 py-2 rounded-lg transition text-sm text-center block">
                    Duplikasi Jabatan
                </a>
                @if($hierarchy->parent)
                <a href="{{ route('organization.show', $hierarchy->parent) }}" 
                   class="w-full bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 px-4 py-2 rounded-lg transition text-sm text-center block">
                    Lihat Jabatan Induk
                </a>
                @endif
                <form action="{{ route('organization.destroy', $hierarchy) }}" method="POST" class="w-full">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            onclick="return confirm('Hapus jabatan {{ $hierarchy->position_name }}?')"
                            class="w-full bg-red-500/20 hover:bg-red-500/30 text-red-300 px-4 py-2 rounded-lg transition text-sm text-center">
                        Hapus Jabatan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .level-badge-1 { background: rgba(139, 92, 246, 0.2); color: #a78bfa; }
    .level-badge-2 { background: rgba(59, 130, 246, 0.2); color: #60a5fa; }
    .level-badge-3 { background: rgba(16, 185, 129, 0.2); color: #34d399; }
    .level-badge-4 { background: rgba(251, 191, 36, 0.2); color: #fbbf24; }
    .level-badge-5 { background: rgba(156, 163, 175, 0.2); color: #9ca3af; }
</style>
@endpush
@endsection