@extends('layouts.app')

@section('title', 'Merchandise Management')
@section('page-title', 'Merchandise')
@section('page-description', 'Manage all merchandise products')

@section('stats')
<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
        </svg>
        <p class="text-gray-400 text-sm">Total Products</p>
    </div>
    <h3 class="text-2xl font-bold text-green-400">{{ $stats['total'] }}</h3>
</div>

<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-gray-400 text-sm">Active</p>
    </div>
    <h3 class="text-2xl font-bold text-green-400">{{ $stats['active'] }}</h3>
</div>

<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-gray-400 text-sm">Low Stock</p>
    </div>
    <h3 class="text-2xl font-bold text-yellow-400">{{ $stats['low_stock'] }}</h3>
</div>

<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
        </svg>
        <p class="text-gray-400 text-sm">Out of Stock</p>
    </div>
    <h3 class="text-2xl font-bold text-red-400">{{ $stats['out_of_stock'] }}</h3>
</div>
@endsection

@section('content')
<div class="flex flex-wrap justify-between items-center gap-4 mb-6">
    <h2 class="text-2xl font-bold text-white">Merchandise Management</h2>
    <div class="flex gap-3">
        <a href="{{ route('merchandise.create') }}" 
           class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Produk
        </a>
    </div>
</div>

@if(session('success'))
<div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-4">
    {{ session('success') }}
</div>
@endif

<!-- Filters -->
<div class="mb-6">
    <form method="GET" action="{{ route('merchandise.index') }}" class="flex flex-wrap gap-3">
        <select name="category" class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white">
            <option class="text-black" value="">All Categories</option>
            @foreach($categories as $cat)
                <option class="text-black" value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                    {{ ucfirst($cat) }}
                </option>
            @endforeach
        </select>
        
        <input type="text" name="search" value="{{ request('search') }}" 
               placeholder="Search product..." 
               class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white w-64">
        
        <button type="submit" class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded-lg transition">
            Filter
        </button>
        
        @if(request()->hasAny(['category', 'search']))
        <a href="{{ route('merchandise.index') }}" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg transition">
            Reset
        </a>
        @endif
    </form>
</div>

<!-- Products Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($merchandise as $product)
    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 overflow-hidden hover:border-green-500/50 transition-all duration-300 group">
        <!-- Product Image -->
        <div class="relative h-48 overflow-hidden bg-white/5">
            @if($product->image)
                <img src="{{ Storage::url($product->image) }}" 
                     class="w-full h-full object-cover transition group-hover:scale-105 duration-300"
                     alt="{{ $product->name }}">
            @else
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-700 to-gray-800">
                    <svg class="w-16 h-16 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            @endif
            
            @if($product->stock <= 0)
                <div class="absolute inset-0 bg-black/70 flex items-center justify-center">
                    <span class="text-red-400 font-bold text-lg">SOLD OUT</span>
                </div>
            @elseif($product->stock < 10)
                <div class="absolute top-2 right-2 bg-yellow-500 text-black text-xs px-2 py-1 rounded-full">
                    Stock: {{ $product->stock }}
                </div>
            @endif
        </div>
        
        <!-- Product Info -->
        <div class="p-4">
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-bold text-white group-hover:text-green-400 transition">
                    {{ $product->name }}
                </h3>
                <span class="text-green-400 font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
            </div>
            
            <p class="text-xs text-gray-400 mb-2">{{ ucfirst($product->category) }}</p>
            
            <div class="flex justify-between items-center text-sm mb-3">
                <span class="text-gray-400">Stock:</span>
                <span class="{{ $product->stock <= 0 ? 'text-red-400' : ($product->stock < 10 ? 'text-yellow-400' : 'text-green-400') }}">
                    {{ $product->stock }} pcs
                </span>
            </div>
            
            <div class="flex flex-wrap gap-1 mb-3">
                @if($product->sizes)
                    @foreach($product->sizes as $size)
                        <span class="text-xs px-2 py-0.5 bg-white/10 rounded">{{ $size }}</span>
                    @endforeach
                @endif
                @if($product->colors)
                    @foreach($product->colors as $color)
                        <span class="text-xs px-2 py-0.5 bg-white/10 rounded">{{ $color }}</span>
                    @endforeach
                @endif
            </div>
            
            <div class="text-xs text-gray-500 mb-3">
                Sold: {{ $product->sold_count }} pcs
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('merchandise.show', $product) }}" 
                   class="flex-1 text-blue-400 hover:text-blue-300 text-center py-1.5 rounded-lg border border-blue-400/30 hover:bg-blue-400/10 transition">
                    Detail
                </a>
                <a href="{{ route('merchandise.edit', $product) }}" 
                   class="flex-1 text-yellow-400 hover:text-yellow-300 text-center py-1.5 rounded-lg border border-yellow-400/30 hover:bg-yellow-400/10 transition">
                    Edit
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full">
        <div class="text-center py-12">
            <svg class="w-24 h-24 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <p class="text-gray-400 text-lg mb-4">Belum ada produk merchandise</p>
            <a href="{{ route('merchandise.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Produk Pertama
            </a>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($merchandise->hasPages())
<div class="mt-6 text-black">
    {{ $merchandise->links() }}
</div>
@endif
@endsection