@extends('layouts.app')

@section('title', 'Merchandise Orders')
@section('page-title', 'Merchandise Orders')
@section('page-description', 'Manage all merchandise orders')

@section('stats')
<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
        </svg>
        <p class="text-gray-400 text-sm">Total Orders</p>
    </div>
    <h3 class="text-2xl font-bold text-green-400">{{ $stats['total'] }}</h3>
</div>

<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-gray-400 text-sm">Pending Payment</p>
    </div>
    <h3 class="text-2xl font-bold text-yellow-400">{{ $stats['pending'] }}</h3>
</div>

<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
        </svg>
        <p class="text-gray-400 text-sm">Processing</p>
    </div>
    <h3 class="text-2xl font-bold text-purple-400">{{ $stats['processing'] }}</h3>
</div>

<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
        </svg>
        <p class="text-gray-400 text-sm">Shipped</p>
    </div>
    <h3 class="text-2xl font-bold text-indigo-400">{{ $stats['shipped'] }}</h3>
</div>

<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-gray-400 text-sm">Delivered</p>
    </div>
    <h3 class="text-2xl font-bold text-green-400">{{ $stats['delivered'] }}</h3>
</div>

<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-gray-400 text-sm">Total Revenue</p>
    </div>
    <h3 class="text-2xl font-bold text-purple-400">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
</div>
@endsection

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-white">Merchandise Order Management</h2>
</div>

@if(session('success'))
<div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-4">
    {{ session('success') }}
</div>
@endif

<!-- Filters -->
<div class="mb-6">
    <form method="GET" action="{{ route('merchandise.orders') }}" class="flex flex-wrap gap-3">
        <select name="status" class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white">
            <option class="text-black" value="">All Status</option>
            @foreach($statuses as $status)
                <option class="text-black" value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>
        
        <input type="text" 
               name="search" 
               value="{{ request('search') }}"
               placeholder="Search invoice/customer..."
               class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white w-64">
        
        <button type="submit" class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded-lg transition">
            Filter
        </button>
        
        @if(request()->hasAny(['status', 'search']))
        <a href="{{ route('merchandise.orders') }}" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg transition">
            Reset
        </a>
        @endif
    </form>
</div>

<!-- Orders Table -->
<div class="overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="border-b border-white/10">
                <th class="text-left py-3 px-4 text-gray-400">Invoice</th>
                <th class="text-left py-3 px-4 text-gray-400">Customer</th>
                <th class="text-left py-3 px-4 text-gray-400">Product</th>
                <th class="text-left py-3 px-4 text-gray-400">Qty</th>
                <th class="text-left py-3 px-4 text-gray-400">Total</th>
                <th class="text-left py-3 px-4 text-gray-400">Status</th>
                <th class="text-left py-3 px-4 text-gray-400">Order Date</th>
                <th class="text-center py-3 px-4 text-gray-400">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr class="border-b border-white/10 hover:bg-white/5 transition">
                <td class="py-3 px-4">
                    <p class="font-semibold text-sm">{{ $order->invoice_number }}</p>
                 </td>
                <td class="py-3 px-4">
                    <div>
                        <p class="font-semibold">{{ $order->participant->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-400">{{ $order->participant->email ?? 'N/A' }}</p>
                    </div>
                 </td>
                <td class="py-3 px-4">
                    <p class="text-sm">{{ $order->merchandise->name ?? 'N/A' }}</p>
                    @if($order->size)
                        <p class="text-xs text-gray-400">Size: {{ $order->size }}</p>
                    @endif
                    @if($order->color)
                        <p class="text-xs text-gray-400">Color: {{ $order->color }}</p>
                    @endif
                 </td>
                <td class="py-3 px-4 text-center">
                    {{ $order->quantity }} pcs
                 </td>
                <td class="py-3 px-4">
                    <p class="font-semibold text-green-400">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                 </td>
                <td class="py-3 px-4">
                    @if($order->status == 'pending')
                        <span class="px-2 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-xs">Pending</span>
                    @elseif($order->status == 'paid')
                        <span class="px-2 py-1 bg-blue-500/20 text-blue-300 rounded-full text-xs">Paid</span>
                    @elseif($order->status == 'processing')
                        <span class="px-2 py-1 bg-purple-500/20 text-purple-300 rounded-full text-xs">Processing</span>
                    @elseif($order->status == 'shipped')
                        <span class="px-2 py-1 bg-indigo-500/20 text-indigo-300 rounded-full text-xs">Shipped</span>
                    @elseif($order->status == 'delivered')
                        <span class="px-2 py-1 bg-green-500/20 text-green-300 rounded-full text-xs">Delivered</span>
                    @else
                        <span class="px-2 py-1 bg-red-500/20 text-red-300 rounded-full text-xs">Cancelled</span>
                    @endif
                 </td>
                <td class="py-3 px-4 text-sm">
                    {{ $order->created_at->format('d/m/Y H:i') }}
                 </td>
                <td class="py-3 px-4">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('merchandise.orders.show', $order) }}" 
                           class="text-blue-400 hover:text-blue-300 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                    </div>
                 </td>
             </tr>
            @empty
             <tr>
                <td colspan="8" class="py-8 text-center text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    <p>No merchandise orders found</p>
                </td>
             </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($orders->hasPages())
<div class="mt-6">
    {{ $orders->appends(request()->query())->links() }}
</div>
@endif
@endsection