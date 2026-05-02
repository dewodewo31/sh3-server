@extends('layouts.app')

@section('title', 'Order Management')
@section('page-title', 'Orders')
@section('page-description', 'Manage all customer orders and payments')

@section('stats')
<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
        </svg>
        <p class="text-gray-400 text-sm">Total Orders</p>
    </div>
    <h3 class="text-2xl font-bold text-green-400">{{ $totalOrders }}</h3>
</div>

<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-gray-400 text-sm">Pending</p>
    </div>
    <h3 class="text-2xl font-bold text-yellow-400">{{ $pendingOrders }}</h3>
</div>

<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
        <p class="text-gray-400 text-sm">Paid</p>
    </div>
    <h3 class="text-2xl font-bold text-green-400">{{ $paidOrders }}</h3>
</div>

<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        <p class="text-gray-400 text-sm">Free</p>
    </div>
    <h3 class="text-2xl font-bold text-blue-400">{{ $freeOrders }}</h3>
</div>

<div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
    <div class="flex items-center gap-2 mb-2">
        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-gray-400 text-sm">Revenue</p>
    </div>
    <h3 class="text-2xl font-bold text-purple-400">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
</div>
@endsection

@section('content')
<div class="flex flex-wrap justify-between items-center gap-4 mb-6">
    <h2 class="text-2xl font-bold text-white">Order Management</h2>

    <!-- Filters -->
    <div class="flex flex-wrap gap-3">
        <form method="GET" action="{{ route('orders.index') }}" class="flex flex-wrap gap-3">
            <select name="status" class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white">
                <option value="">All Status</option>
                @foreach($statuses as $status)
                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                    {{ ucfirst($status) }}
                </option>
                @endforeach
            </select>

            <select name="event_id" class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white">
                <option value="">All Events</option>
                @foreach($events as $event)
                <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                    {{ $event->title }}
                </option>
                @endforeach
            </select>

             <input type="month" name="month" value="{{ request('month') }}" class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white">

            <input type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search invoice/ticket/name..."
                class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white w-64">

            <button type="submit" class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded-lg transition">
                Filter
            </button>
            <!-- Di samping tombol Tambah Order atau filter -->
            <a href="{{ route('orders.export-all-pdf') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export All PDF
            </a>

            @if(request()->hasAny(['status', 'event_id', 'search', 'month']))
            <a href="{{ route('orders.index') }}" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg transition">
                Reset
            </a>
            @endif
        </form>
    </div>
</div>

@if(session('success'))
<div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-4">
    {{ session('success') }}
</div>
@endif

<!-- Orders Table -->
<div class="overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="border-b border-white/10">
                <th class="text-left py-3 px-4 text-gray-400">Invoice</th>
                <th class="text-left py-3 px-4 text-gray-400">Customer</th>
                <th class="text-left py-3 px-4 text-gray-400">Event</th>
                <th class="text-left py-3 px-4 text-gray-400">Total</th>
                <th class="text-left py-3 px-4 text-gray-400">Status</th>
                <th class="text-left py-3 px-4 text-gray-400">Date</th>
                <th class="text-center py-3 px-4 text-gray-400">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr class="border-b border-white/10 hover:bg-white/5 transition">
                <td class="py-3 px-4">
                    <div>
                        <p class="font-semibold text-sm">{{ $order->invoice_number }}</p>
                        <p class="text-xs text-gray-400">Ticket: {{ $order->ticket_code }}</p>
                    </div>
                </td>
                <td class="py-3 px-4">
                    <div>
                        <p class="font-semibold">{{ $order->participant->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-400">{{ $order->participant->email ?? 'N/A' }}</p>
                    </div>
                </td>
                <td class="py-3 px-4">
                    <p class="text-sm">{{ Str::limit($order->event->title ?? 'N/A', 40) }}</p>
                </td>
                <td class="py-3 px-4">
                    @if($order->total_price > 0)
                    <p class="font-semibold text-green-400">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                    @else
                    <p class="text-green-400">GRATIS</p>
                    @endif
                </td>
                <td class="py-3 px-4">
                    @if($order->status == 'pending')
                    <span class="px-2 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-xs">Pending</span>
                    @elseif($order->status == 'paid')
                    <span class="px-2 py-1 bg-green-500/20 text-green-300 rounded-full text-xs">Paid</span>
                    @elseif($order->status == 'free')
                    <span class="px-2 py-1 bg-blue-500/20 text-blue-300 rounded-full text-xs">Free</span>
                    @elseif($order->status == 'cancelled')
                    <span class="px-2 py-1 bg-red-500/20 text-red-300 rounded-full text-xs">Cancelled</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-sm">
                    {{ $order->created_at->format('d/m/Y H:i') }}
                </td>
                <td class="py-3 px-4">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('orders.show', $order) }}"
                            class="text-blue-400 hover:text-blue-300 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>
                        @if($order->status != 'paid' && $order->status != 'cancelled' && $order->status != 'free')
                        <form action="{{ route('orders.cancel', $order) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                onclick="return confirm('Yakin ingin membatalkan order ini?')"
                                class="text-red-400 hover:text-red-300 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-8 text-center text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <p>No orders found</p>
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