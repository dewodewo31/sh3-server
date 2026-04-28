@extends('layouts.app')

@section('title', 'Order Details - ' . $order->invoice_number)
@section('page-title', 'Order Details')
@section('page-description', 'View complete order information')

@section('content')
<div class="mb-6">
    <div class="flex justify-between items-center flex-wrap gap-4">
        <a href="{{ route('orders.index') }}" class="text-gray-400 hover:text-white transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Orders
        </a>
        
        <div class="flex gap-2">
            @if($order->status != 'paid' && $order->status != 'cancelled' && $order->status != 'free')
            <form action="{{ route('orders.cancel', $order) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin membatalkan order ini?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                    Cancel Order
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-4">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-500/20 border border-red-500 text-red-300 px-4 py-3 rounded-lg mb-4">
        {{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Order Information -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Order Header -->
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <div class="flex justify-between items-start mb-4 flex-wrap gap-3">
                <div>
                    <h2 class="text-2xl font-bold text-white">{{ $order->invoice_number }}</h2>
                    <p class="text-gray-400 text-sm mt-1">Ticket Code: <span class="font-mono text-green-400">{{ $order->ticket_code }}</span></p>
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
                <div>
                    <p class="text-gray-400 text-sm">Phone Number</p>
                    <p class="text-white font-semibold">{{ $order->participant->phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Hash ID</p>
                    <code class="text-green-400 font-mono text-sm">{{ $order->participant->hash_id ?? 'N/A' }}</code>
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
                @if($order->event && $order->event->image)
                    <img src="{{ Storage::url($order->event->image) }}" class="w-20 h-20 rounded-lg object-cover">
                @else
                    <div class="w-20 h-20 rounded-lg bg-gradient-to-br from-green-500/20 to-blue-500/20 flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
                <div class="flex-1">
                    <h4 class="font-semibold text-white">{{ $order->event->title ?? 'N/A' }}</h4>
                    <p class="text-sm text-gray-400">{{ $order->event->location ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-400">
                        {{ $order->event->start_date ? $order->event->start_date->format('d M Y') : 'N/A' }} - 
                        {{ $order->event->end_date ? $order->event->end_date->format('d M Y') : 'N/A' }}
                    </p>
                </div>
            </div>
            
            @if($order->event && $order->event->key_point)
            <div class="mt-3 pt-3 border-t border-white/10">
                <p class="text-gray-400 text-sm mb-2">Key Points:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($order->event->key_point as $point)
                        <span class="px-2 py-1 bg-green-500/10 text-green-300 rounded-full text-xs">{{ $point }}</span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        
        <!-- Payment Proof -->
        @if($order->payment)
        <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Payment Information
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
                        <p class="text-white">{{ $order->payment->paid_at ? \Carbon\Carbon::parse($order->payment->paid_at)->format('d M Y, H:i') : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Payment Status</p>
                        <div class="mt-1">
                            @if($order->payment->status == 'pending')
                                <span class="px-2 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-xs">Pending Verification</span>
                            @elseif($order->payment->status == 'confirmed')
                                <span class="px-2 py-1 bg-green-500/20 text-green-300 rounded-full text-xs">Confirmed</span>
                            @else
                                <span class="px-2 py-1 bg-red-500/20 text-red-300 rounded-full text-xs">Rejected</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div>
                    <p class="text-gray-400 text-sm mb-2">Payment Proof</p>
                    <div class="bg-white/5 rounded-lg p-2 cursor-pointer" onclick="openProofModal('{{ Storage::url($order->payment->payment_proof) }}')">
                        <img src="{{ Storage::url($order->payment->payment_proof) }}" 
                             class="w-full rounded-lg">
                        <p class="text-xs text-center text-gray-400 mt-1">Click to enlarge</p>
                    </div>
                </div>
            </div>
            
            @if($order->payment->verified_at)
            <div class="mt-4 pt-4 border-t border-white/10">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-400 text-sm">Verified By</p>
                        <p class="text-white">{{ $order->payment->verifier->name ?? 'Admin' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Verified At</p>
                        <p class="text-white">{{ \Carbon\Carbon::parse($order->payment->verified_at)->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            </div>
            @endif
            
            @if($order->payment->notes)
            <div class="mt-4 pt-4 border-t border-white/10">
                <p class="text-gray-400 text-sm">Notes</p>
                <p class="text-white text-sm bg-black/20 rounded-lg p-2 mt-1">{{ $order->payment->notes }}</p>
            </div>
            @endif
        </div>
        @endif
    </div>
    
    <!-- Action Sidebar -->
    <div class="space-y-6">
        @if($order->status == 'pending' && $order->total_price > 0)
        <!-- Verification Card for Pending Payment -->
        <div class="bg-gradient-to-br from-yellow-500/10 to-yellow-600/10 rounded-xl border border-yellow-500/30 p-6 sticky top-6">
            <h3 class="text-lg font-bold text-yellow-400 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Verify Payment
            </h3>
            
            <form action="{{ route('orders.verify-payment', $order) }}" method="POST">
                @csrf
                @method('PATCH')
                
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 text-sm">Verification Status</label>
                    <select name="status" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white">
                        <option value="paid">✓ Confirm - Payment Valid</option>
                        <option value="cancelled">✗ Reject - Payment Invalid</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2 text-sm">Notes (Optional)</label>
                    <textarea name="notes" rows="3" class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-white" 
                              placeholder="Add verification notes..."></textarea>
                </div>
                
                <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition font-semibold">
                    Submit Verification
                </button>
            </form>
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
        <div class="bg-gradient-to-br from-green-500/10 to-green-600/10 rounded-xl border border-green-500/30 p-6 sticky top-6">
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
        <div class="bg-gradient-to-br from-blue-500/10 to-blue-600/10 rounded-xl border border-blue-500/30 p-6 sticky top-6">
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
        <div class="bg-gradient-to-br from-red-500/10 to-red-600/10 rounded-xl border border-red-500/30 p-6 sticky top-6">
            <div class="text-center">
                <svg class="w-16 h-16 mx-auto mb-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <h3 class="text-xl font-bold text-red-400 mb-2">Order Cancelled</h3>
                <p class="text-gray-300 text-sm">This order has been cancelled.</p>
            </div>
        </div>
        @endif
        
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
                
                @if($order->payment && $order->payment->paid_at)
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-blue-500/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        @if($order->payment->status == 'confirmed')
                            <div class="w-0.5 h-8 bg-blue-500/30 mt-1"></div>
                        @endif
                    </div>
                    <div>
                        <p class="font-semibold text-white">Payment Made</p>
                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($order->payment->paid_at)->format('d M Y, H:i:s') }}</p>
                    </div>
                </div>
                @endif
                
                @if($order->payment && $order->payment->verified_at)
                <div class="flex gap-3">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-purple-500/20 flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="font-semibold text-white">Payment Verified</p>
                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($order->payment->verified_at)->format('d M Y, H:i:s') }}</p>
                        <p class="text-xs text-gray-500">By: {{ $order->payment->verifier->name ?? 'Admin' }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Payment Proof Modal -->
<div id="proofModal" class="fixed inset-0 bg-black/95 z-50 hidden items-center justify-center p-4" onclick="closeProofModal()">
    <div class="relative max-w-4xl w-full" onclick="event.stopPropagation()">
        <img id="proofImage" src="" class="w-full h-auto rounded-lg max-h-[85vh] object-contain">
        <button onclick="closeProofModal()" class="absolute top-4 right-4 text-white bg-black/50 hover:bg-black/70 rounded-full p-2 transition">
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