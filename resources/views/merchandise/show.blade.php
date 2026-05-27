@extends('layouts.app')

@section('title', 'Product Details - ' . $merchandise->name)
@section('page-title', 'Product Details')
@section('page-description', 'View complete product information')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <a href="{{ route('merchandise.index') }}" class="text-gray-400 hover:text-white transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Products
        </a>
        
        <div class="flex gap-2">
            <a href="{{ route('merchandise.edit', $merchandise) }}" 
               class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Product
            </a>
            <form action="{{ route('merchandise.destroy', $merchandise) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Delete Product
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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Product Image Card -->
    <div class="lg:col-span-1">
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6 sticky top-6">
            <!-- Product Image -->
            <div class="mb-6">
                @if($merchandise->image)
                    <img src="{{ Storage::url($merchandise->image) }}" 
                         class="w-full rounded-xl object-cover border-2 border-green-500/30"
                         alt="{{ $merchandise->name }}">
                @else
                    <div class="w-full h-64 bg-gradient-to-br from-gray-700 to-gray-800 rounded-xl flex items-center justify-center">
                        <svg class="w-24 h-24 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
            </div>
            
            <!-- Product Info -->
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-white">{{ $merchandise->name }}</h2>
                <div class="inline-flex px-3 py-1 rounded-full text-sm mt-2 bg-green-500/20 text-green-300">
                    {{ ucfirst($merchandise->category) }}
                </div>
            </div>
            
            <!-- Pricing & Stock -->
            <div class="border-t border-white/10 pt-4 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Price</span>
                    <span class="text-2xl font-bold text-green-400">Rp {{ number_format($merchandise->price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Stock</span>
                    <span class="{{ $merchandise->stock <= 0 ? 'text-red-400' : ($merchandise->stock < 10 ? 'text-yellow-400' : 'text-green-400') }} font-semibold">
                        {{ $merchandise->stock }} pcs
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Sold</span>
                    <span class="text-white">{{ $merchandise->sold_count }} pcs</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Status</span>
                    <span class="{{ $merchandise->is_active ? 'text-green-400' : 'text-red-400' }}">
                        {{ $merchandise->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
            
            <!-- Sizes & Colors -->
            @if($merchandise->sizes)
            <div class="border-t border-white/10 pt-4 mt-4">
                <h3 class="text-sm font-semibold text-gray-300 mb-2">Available Sizes</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($merchandise->sizes as $size)
                        <span class="px-3 py-1 bg-white/10 rounded-lg text-sm">{{ $size }}</span>
                    @endforeach
                </div>
            </div>
            @endif
            
            @if($merchandise->colors)
            <div class="border-t border-white/10 pt-4 mt-4">
                <h3 class="text-sm font-semibold text-gray-300 mb-2">Available Colors</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($merchandise->colors as $color)
                        <span class="px-3 py-1 bg-white/10 rounded-lg text-sm">{{ $color }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Product Details & Orders -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Description -->
        @if($merchandise->description)
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/>
                </svg>
                Description
            </h3>
            <div class="text-gray-300 leading-relaxed">
                {{ $merchandise->description }}
            </div>
        </div>
        @endif

        <!-- Order Statistics -->
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-4 text-center">
                <p class="text-2xl font-bold text-blue-400">{{ $totalOrders }}</p>
                <p class="text-xs text-gray-400">Total Orders</p>
            </div>
            <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-4 text-center">
                <p class="text-2xl font-bold text-green-400">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-400">Total Revenue</p>
            </div>
        </div>

        <!-- Order History -->
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Order History
            </h3>
            
            @if($merchandise->orders->count() > 0)
                <div class="space-y-3">
                    @foreach($merchandise->orders as $order)
                    <div class="bg-white/5 rounded-lg p-3 hover:bg-white/10 transition">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-semibold text-white">{{ $order->invoice_number }}</p>
                                <p class="text-xs text-gray-400">{{ $order->participant->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">Qty: {{ $order->quantity }} pcs</p>
                            </div>
                            <div class="text-right">
                                <p class="text-green-400 font-semibold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                                <span class="px-2 py-1 text-xs rounded-full {{ $order->status_badge }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <p class="text-gray-400">No orders yet</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection