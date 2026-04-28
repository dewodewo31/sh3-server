@extends('layouts.app')

@section('title', 'Order Details - ' . $order->invoice_number)
@section('page-title', 'Order Details')
@section('page-description', 'View and manage order information')

@section('content')
<div class="mb-6">
    <a href="{{ route('orders.index') }}" class="text-gray-400 hover:text-white transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Orders
    </a>
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
                    <p class="text-gray-400 text-sm">Ticket Code: <span class="font-mono">{{ $order->ticket_code }}</span></p>
                </div>
                <div class="text-right">
                    @if($order->status == 'pending')
                        <span class="px-3 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-sm">Pending Payment</span>
                    @elseif($order->status == 'paid')
                        <span class="px-3 py-1 bg-green-500/20 text-green-300 rounded-full text-sm">Paid ✓</span>
                    @elseif($order->status == 'free')
                        <span class="px-3 py-1 bg-blue-500/20 text-blue-300 rounded-full text-sm">Free Event</span>
                    @else
                        <span class="px-3 py-1 bg-red-500/20 text-red-300 rounded-full text-sm">Cancelled</span>
                    @endif
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-white/10">
                <div>
                    <p class="text-gray-400 text-xs">Order Date</p>
                    <p class="text-white font-semibold">{{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs">Payment Status</p>
                    <p class="text-white font-semibold">{{ ucfirst($order->status) }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs">Total Amount</p>
                    @if($order->total_price > 0)
                        <p class="text-green-400 font-bold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                    @else
                        <p class="text-green-400 font-bold">FREE</p>
                    @endif
                </div>
                <div>
                    <p class="text-gray-400 text-xs">Payment Method</p>
                    <p class="text-white font-semibold">{{ $order->payment->payment_method ?? '-' }}</p>
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
            </div>
        </div>
        
        <!-- Event Information -->
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Event Information
            </h3>
            <div class="flex gap-4 mb-4">
                @if($order->event->image)
                    <img src="{{ Storage::url($order->event->image) }}" class="w-20 h-20 rounded-lg object-cover">
                @else
                    <div class="w-20 h-20 rounded-lg bg-gradient-to-br from-green-500/20 to-blue-500/20 flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
                <div class="flex-1">
                    <h4 class="font-semibold text-white">{{ $order->event->title }}</h4>
                    <p class="text-sm text-gray-400">{{ $order->event->location }}</p>
                    <p class="text-sm text-gray-400">
                        {{ $order->event->start_date->format('d M Y') }} - {{ $order->event->end_date->format('d M Y') }}
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Payment Proof -->
        @if($order->payment)
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Payment Proof
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="mb-4">
                        <p class="text-gray-400 text-sm">Payment Method</p>
                        <p class="text-white font-semibold">{{ $order->payment->payment_method }}</p>
                    </div>
                    <div class="mb-4">
                        <p class="text-gray-400 text-sm">Amount Paid</p>
                        <p class="text-green-400 font-bold">Rp {{ number_format($order->payment->amount, 0, ',', '.') }}</p>
                    </div>
                    <div class="mb-4">
                        <p class="text-gray-400 text-sm">Payment Date</p>
                        <p class="text-white">{{ \Carbon\Carbon::parse($order->payment->paid_at)->format('d M Y, H:i') }}</p>
                    </div>
                    @if($order->payment->verified_at)
                    <div>
                        <p class="text-gray-400 text-sm">Verified By</p>
                        <p class="text-white">{{ $order->payment->verifier->name ?? 'Admin' }} at {{ \Carbon\Carbon::parse($order->payment->verified_at)->format('d M Y, H:i') }}</p>
                    </div>
                    @endif
                </div>
                
                <div>
                    <p class="text-gray-400 text-sm mb-2">Proof Image</p>
                    <div class="bg-white/5 rounded-lg p-2">
                        <img src="{{ Storage::url($order->payment->payment_proof) }}" 
                             class="w-full rounded-lg cursor-pointer"
                             onclick="openProofModal('{{ Storage::url($order->payment->payment_proof) }}')">
                    </div>
                </div>
            </div>
            
            @if($order->payment->notes)
            <div class="mt-4 pt-4 border-t border-white/10">
                <p class="text-gray-400 text-sm">Notes</p>
                <p class="text-white">{{ $order->payment->notes }}</p>
            </div>
            @endif
        </div>
        @endif
    </div>
    
    <!-- Action Sidebar -->
    <div class="space-y-6">
        @if($order->status == 'pending' && $order->total_price > 0)
        <!-- Payment Pending Card -->
        <div class="bg-gradient-to-br from-yellow-500/10 to-yellow-600/10 rounded-xl border border-yellow-500/30 p-6">
            <div class="text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-xl font-bold text-yellow-400 mb-2">Pending Payment</h3>
                <p class="text-gray-300 text-sm">Customer has not uploaded payment proof yet.</p>
                <p class="text-gray-400 text-xs mt-2">Waiting for customer to complete payment.</p>
            </div>
        </div>
        
        <!-- Cancel Button for Pending Order -->
        <div class="bg-red-500/10 rounded-xl border border-red-500/30 p-6">
            <h3 class="text-lg font-bold text-red-400 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Cancel Order
            </h3>
            <p class="text-gray-400 text-sm mb-4">Cancel this pending order.</p>
            <form action="{{ route('orders.cancel', $order) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" 
                        onclick="return confirm('Are you sure? This will cancel the order.')"
                        class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition">
                    Cancel Order
                </button>
            </form>
        </div>
        @elseif($order->status == 'paid')
        <!-- Paid Info Card -->
        <div class="bg-gradient-to-br from-green-500/10 to-green-600/10 rounded-xl border border-green-500/30 p-6">
            <div class="text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-xl font-bold text-green-400 mb-2">Payment Confirmed</h3>
                <p class="text-gray-300 text-sm">Order has been paid and confirmed.</p>
                <p class="text-gray-400 text-xs mt-2">Ticket has been issued to customer.</p>
            </div>
        </div>
        @elseif($order->status == 'free')
        <!-- Free Event Info Card -->
        <div class="bg-gradient-to-br from-blue-500/10 to-blue-600/10 rounded-xl border border-blue-500/30 p-6">
            <div class="text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-xl font-bold text-blue-400 mb-2">Free Event</h3>
                <p class="text-gray-300 text-sm">This is a free event.</p>
                <p class="text-gray-400 text-xs mt-2">No payment required.</p>
            </div>
        </div>
        @elseif($order->status == 'cancelled')
        <!-- Cancelled Info Card -->
        <div class="bg-gradient-to-br from-red-500/10 to-red-600/10 rounded-xl border border-red-500/30 p-6">
            <div class="text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <h3 class="text-xl font-bold text-red-400 mb-2">Order Cancelled</h3>
                <p class="text-gray-300 text-sm">This order has been cancelled.</p>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Proof Image Modal -->
<div id="proofModal" class="fixed inset-0 bg-black/90 z-50 hidden items-center justify-center p-4" onclick="closeProofModal()">
    <div class="relative max-w-4xl w-full" onclick="event.stopPropagation()">
        <img id="proofImage" src="" class="w-full h-auto rounded-lg">
        <button onclick="closeProofModal()" class="absolute top-4 right-4 text-white bg-black/50 rounded-full p-2 hover:bg-black/70">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>

@push('scripts')
<script>
    function openProofModal(imageUrl) {
        const modal = document.getElementById('proofModal');
        const img = document.getElementById('proofImage');
        img.src = imageUrl;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    
    function closeProofModal() {
        const modal = document.getElementById('proofModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeProofModal();
        }
    });
</script>
@endpush
@endsection