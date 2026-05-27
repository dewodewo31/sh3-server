@extends('layouts.app')

@section('title', 'Order Details - ' . $order->invoice_number)
@section('page-title', 'Merchandise Order Details')
@section('page-description', 'View complete merchandise order information')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <a href="{{ route('merchandise.orders') }}" class="text-gray-400 hover:text-white transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Orders
        </a>
        
        <div class="flex gap-2">
            <form action="{{ route('merchandise.orders.update-status', $order) }}" method="POST" class="inline">
                @csrf
                <select name="status" onchange="this.form.submit()" class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white">
                    <option class="text-black" value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option class="text-black" value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option class="text-black" value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option class="text-black" value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option class="text-black" value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option class="text-black" value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
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
    <!-- Order Information -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Order Header -->
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-white">{{ $order->invoice_number }}</h2>
                    <p class="text-gray-400 text-sm">Order ID: #{{ $order->id }}</p>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 rounded-full text-sm
                        @if($order->status == 'pending') bg-yellow-500/20 text-yellow-300
                        @elseif($order->status == 'paid') bg-blue-500/20 text-blue-300
                        @elseif($order->status == 'processing') bg-purple-500/20 text-purple-300
                        @elseif($order->status == 'shipped') bg-indigo-500/20 text-indigo-300
                        @elseif($order->status == 'delivered') bg-green-500/20 text-green-300
                        @else bg-red-500/20 text-red-300 @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-white/10">
                <div>
                    <p class="text-gray-400 text-xs">Order Date</p>
                    <p class="text-white font-semibold">{{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs">Quantity</p>
                    <p class="text-white font-semibold">{{ $order->quantity }} pcs</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs">Total Amount</p>
                    <p class="text-green-400 font-bold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs">Unit Price</p>
                    <p class="text-white">Rp {{ number_format($order->unit_price, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Product Information -->
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Product Information
            </h3>
            
            <div class="flex gap-4">
                @if($order->merchandise && $order->merchandise->image)
                    <img src="{{ Storage::url($order->merchandise->image) }}" 
                         class="w-24 h-24 rounded-lg object-cover">
                @else
                    <div class="w-24 h-24 bg-white/5 rounded-lg flex items-center justify-center">
                        <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
                <div class="flex-1">
                    <h4 class="font-semibold text-white">{{ $order->merchandise->name ?? 'Product Not Found' }}</h4>
                    <p class="text-sm text-gray-400">{{ ucfirst($order->merchandise->category ?? 'N/A') }}</p>
                    @if($order->size)
                        <p class="text-sm text-gray-400">Size: <span class="text-white">{{ $order->size }}</span></p>
                    @endif
                    @if($order->color)
                        <p class="text-sm text-gray-400">Color: <span class="text-white">{{ $order->color }}</span></p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Customer Information -->
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Customer Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-400 text-sm">Full Name</p>
                    <p class="text-white font-semibold">{{ $order->participant->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Email Address</p>
                    <p class="text-white font-semibold">{{ $order->participant->email ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Phone</p>
                    <p class="text-white">{{ $order->shipping_phone ?? $order->participant->phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Hash ID</p>
                    <code class="text-green-400 text-sm">{{ $order->participant->hash_id ?? 'N/A' }}</code>
                </div>
            </div>
        </div>
        
        <!-- Shipping Information -->
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Shipping Information
            </h3>
            
            <div class="space-y-3">
                <div>
                    <p class="text-gray-400 text-sm">Shipping Address</p>
                    <p class="text-white">{{ $order->shipping_address ?? 'Not specified' }}</p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-400 text-sm">Shipping Phone</p>
                        <p class="text-white">{{ $order->shipping_phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Shipping Courier</p>
                        <p class="text-white">{{ $order->shipping_courier ?? '-' }}</p>
                    </div>
                </div>
                
                <div>
                    <p class="text-gray-400 text-sm">Tracking Number</p>
                    @if($order->tracking_number)
                        <p class="text-green-400 font-mono">{{ $order->tracking_number }}</p>
                    @else
                        <p class="text-gray-500">-</p>
                    @endif
                </div>
                
                @if($order->notes)
                <div class="mt-3 pt-3 border-t border-white/10">
                    <p class="text-gray-400 text-sm">Customer Notes</p>
                    <p class="text-white text-sm bg-white/5 rounded-lg p-2">{{ $order->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Action Sidebar -->
    <div class="space-y-6">
        <!-- Status Update Card -->
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6 sticky top-6">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Update Order Status
            </h3>
            
            <form action="{{ route('merchandise.orders.update-status', $order) }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 text-sm">Order Status</label>
                    <select name="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white">
                        <option class="text-black" value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending Payment</option>
                        <option class="text-black" value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Paid - Waiting Processing</option>
                        <option class="text-black" value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option class="text-black" value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                        <option class="text-black" value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option class="text-black" value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 text-sm">Shipping Courier</label>
                    <input type="text" name="shipping_courier" value="{{ $order->shipping_courier }}" 
                           class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white"
                           placeholder="JNE, J&T, SiCepat, etc">
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 text-sm">Tracking Number</label>
                    <input type="text" name="tracking_number" value="{{ $order->tracking_number }}" 
                           class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white font-mono"
                           placeholder="Enter tracking number">
                </div>
                
                <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition font-semibold">
                    Update Order
                </button>
            </form>
        </div>
        
        <!-- Order Timeline -->
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Order Timeline
            </h3>
            
            <div class="space-y-4">
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        @if($order->status != 'pending')
                            <div class="w-0.5 h-8 bg-green-500/30 mt-1"></div>
                        @endif
                    </div>
                    <div>
                        <p class="font-semibold text-white">Order Created</p>
                        <p class="text-xs text-gray-400">{{ $order->created_at->format('d M Y, H:i:s') }}</p>
                    </div>
                </div>
                
                @if($order->paid_at)
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        @if(in_array($order->status, ['processing', 'shipped', 'delivered']))
                            <div class="w-0.5 h-8 bg-blue-500/30 mt-1"></div>
                        @endif
                    </div>
                    <div>
                        <p class="font-semibold text-white">Payment Confirmed</p>
                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($order->paid_at)->format('d M Y, H:i:s') }}</p>
                    </div>
                </div>
                @endif
                
                @if($order->shipped_at)
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-purple-500/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        @if($order->status == 'delivered')
                            <div class="w-0.5 h-8 bg-purple-500/30 mt-1"></div>
                        @endif
                    </div>
                    <div>
                        <p class="font-semibold text-white">Order Shipped</p>
                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($order->shipped_at)->format('d M Y, H:i:s') }}</p>
                        @if($order->tracking_number)
                            <p class="text-xs text-gray-500">Tracking: {{ $order->tracking_number }}</p>
                        @endif
                    </div>
                </div>
                @endif
                
                @if($order->delivered_at)
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="font-semibold text-white">Order Delivered</p>
                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($order->delivered_at)->format('d M Y, H:i:s') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Payment Instructions (for pending orders) -->
        @if($order->status == 'pending')
        <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-lg p-4">
            <h4 class="text-yellow-400 font-semibold mb-2">Payment Instructions</h4>
            <p class="text-sm text-gray-300">Transfer to:</p>
            <p class="text-sm font-mono text-white">BCA - 1234567890 a.n SH3 Event</p>
            <p class="text-sm font-mono text-white">Mandiri - 0987654321 a.n SH3 Event</p>
            <p class="text-xs text-gray-400 mt-2">Upload payment proof to complete your order.</p>
        </div>
        @endif
    </div>
</div>
@endsection