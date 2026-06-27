@extends('layouts.app')

@section('title', 'Organisation Hierarchy')
@section('page-title', 'Organisation Structure')
@section('page-description', 'Manage organisation hierarchy and positions')

@push('styles')
<style>
    .tree-node {
        transition: all 0.2s ease;
    }
    .tree-node:hover {
        background: rgba(255,255,255,0.05);
    }
    .level-badge-1 { background: rgba(139, 92, 246, 0.2); color: #a78bfa; }
    .level-badge-2 { background: rgba(59, 130, 246, 0.2); color: #60a5fa; }
    .level-badge-3 { background: rgba(16, 185, 129, 0.2); color: #34d399; }
    .level-badge-4 { background: rgba(251, 191, 36, 0.2); color: #fbbf24; }
    .level-badge-5 { background: rgba(156, 163, 175, 0.2); color: #9ca3af; }
    .tree-line {
        border-left: 2px solid rgba(255,255,255,0.1);
        margin-left: 20px;
        padding-left: 20px;
    }
    .tree-connector {
        position: relative;
    }
    .tree-connector::before {
        content: '';
        position: absolute;
        left: -22px;
        top: 20px;
        width: 20px;
        height: 2px;
        background: rgba(255,255,255,0.1);
    }
</style>
@endpush

@section('content')
<div class="mb-6">
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div class="flex flex-wrap items-center gap-3">
            <!-- Year Filter -->
            <div class="flex items-center gap-2">
                <label class="text-gray-400 text-sm font-medium">Tahun:</label>
                <select name="year" id="yearFilter" 
                        class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-green-500 text-sm">
                    @foreach($years as $y)
                        <option class="text-black" value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Level Filter -->
            <div class="flex items-center gap-2">
                <label class="text-gray-400 text-sm font-medium">Level:</label>
                <select name="level" id="levelFilter" 
                        class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white focus:outline-none focus:border-green-500 text-sm">
                    <option class="text-black" value="">Semua Level</option>
                    @foreach($levels as $level)
                        <option class="text-black" value="{{ $level->level }}">
                            {{ $level->level_name ?? 'Level ' . $level->level }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <button onclick="applyFilters()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition text-sm">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filter
            </button>
            
            @if(request()->has('level'))
            <a href="{{ route('organization.index', ['year' => $year]) }}" class="text-gray-400 hover:text-white text-sm">
                Reset Filter
            </a>
            @endif
        </div>
        
        <div class="flex gap-2 flex-wrap">
            <button onclick="openDuplicateModal()" 
                    class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-2 rounded-lg transition flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                Duplikasi Tahun
            </button>
            
            <a href="{{ route('organization.create') }}" 
               class="bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded-lg transition flex items-center gap-2 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Jabatan
            </a>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white/5 rounded-lg border border-white/10 p-4 text-center">
        <p class="text-2xl font-bold text-purple-400">{{ $stats['total_positions'] ?? 0 }}</p>
        <p class="text-xs text-gray-400">Total Jabatan</p>
    </div>
    <div class="bg-white/5 rounded-lg border border-white/10 p-4 text-center">
        <p class="text-2xl font-bold text-blue-400">{{ $stats['total_holders'] ?? 0 }}</p>
        <p class="text-xs text-gray-400">Total Pemegang</p>
    </div>
    <div class="bg-white/5 rounded-lg border border-white/10 p-4 text-center">
        <p class="text-2xl font-bold text-yellow-400">{{ $stats['total_levels'] ?? 0 }}</p>
        <p class="text-xs text-gray-400">Total Level</p>
    </div>
    <div class="bg-white/5 rounded-lg border border-white/10 p-4 text-center">
        <p class="text-2xl font-bold text-green-400">{{ $year }}</p>
        <p class="text-xs text-gray-400">Tahun Aktif</p>
    </div>
</div>

<!-- Tree View -->
<div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-white flex items-center gap-2">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Struktur Organisasi {{ $year }}
        </h3>
        <span class="text-xs text-gray-400">{{ count($tree) }} root nodes</span>
    </div>
    
    @if(count($tree) > 0)
        <div class="space-y-1">
            @foreach($tree as $node)
                @include('organization.partials.tree-node', ['node' => $node, 'level' => 0])
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <p class="text-gray-400">Belum ada data struktur untuk tahun {{ $year }}</p>
            <a href="{{ route('organization.create') }}" class="text-green-400 hover:text-green-300 mt-2 inline-block text-sm">
                + Tambah jabatan pertama
            </a>
        </div>
    @endif
</div>

<!-- Duplicate Modal -->
<div id="duplicateModal" class="fixed inset-0 bg-black/70 z-50 hidden items-center justify-center p-4" onclick="if(event.target===this) closeDuplicateModal()">
    <div class="bg-gray-900 rounded-xl max-w-md w-full border border-white/10">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Duplikasi Tahun
                </h3>
                <button onclick="closeDuplicateModal()" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <form action="{{ route('organization.duplicate-year') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 font-medium">Tahun Sumber *</label>
                    <select name="source_year" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-purple-500" required>
                        @foreach($years as $y)
                            <option class="text-black" value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 font-medium">Tahun Tujuan *</label>
                    <select name="target_year" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white focus:outline-none focus:border-purple-500" required>
                        @for($y = date('Y') + 1; $y >= date('Y') - 5; $y--)
                            <option class="text-black" value="{{ $y }}" {{ $y == date('Y') + 1 ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                    <p class="text-xs text-gray-500 mt-1">⚠️ Pastikan tahun tujuan belum memiliki data</p>
                </div>
                
                <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-3 mb-4">
                    <p class="text-xs text-yellow-300">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Semua jabatan dan pemegang dari tahun sumber akan diduplikasi ke tahun tujuan
                    </p>
                </div>
                
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-purple-500 hover:bg-purple-600 text-white px-4 py-2.5 rounded-lg transition font-medium">
                        Duplikasi
                    </button>
                    <button type="button" onclick="closeDuplicateModal()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2.5 rounded-lg transition font-medium">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function applyFilters() {
        const year = document.getElementById('yearFilter').value;
        const level = document.getElementById('levelFilter').value;
        let url = '{{ route("organization.index") }}?year=' + year;
        if (level) url += '&level=' + level;
        window.location.href = url;
    }
    
    function openDuplicateModal() {
        document.getElementById('duplicateModal').classList.remove('hidden');
        document.getElementById('duplicateModal').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    
    function closeDuplicateModal() {
        document.getElementById('duplicateModal').classList.add('hidden');
        document.getElementById('duplicateModal').classList.remove('flex');
        document.body.style.overflow = 'auto';
    }
    
    // Enter key untuk filter
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            const active = document.activeElement;
            if (active && (active.id === 'yearFilter' || active.id === 'levelFilter')) {
                applyFilters();
            }
        }
        if (e.key === 'Escape') {
            closeDuplicateModal();
        }
    });
</script>
@endsection