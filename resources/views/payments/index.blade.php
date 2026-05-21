@extends('layouts.app')

@section('title', 'Payment Management')
@section('page-title', 'Payments')
@section('page-description', 'Manage all customer payment proofs')

@section('stats')
    <div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            <p class="text-gray-400 text-sm">Total Payments</p>
        </div>
        <h3 class="text-2xl font-bold text-green-400">{{ $totalPayments }}</h3>
    </div>

    <div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-gray-400 text-sm">Pending Verification</p>
        </div>
        <h3 class="text-2xl font-bold text-yellow-400">{{ $pendingPayments }}</h3>
    </div>

    <div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            <p class="text-gray-400 text-sm">Confirmed</p>
        </div>
        <h3 class="text-2xl font-bold text-green-400">{{ $confirmedPayments }}</h3>
    </div>

    <div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <p class="text-gray-400 text-sm">Rejected</p>
        </div>
        <h3 class="text-2xl font-bold text-red-400">{{ $rejectedPayments }}</h3>
    </div>

    <div class="bg-white/5 p-4 rounded-xl border border-white/10 hover:bg-white/10 transition">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-gray-400 text-sm">Total Amount</p>
        </div>
        <h3 class="text-2xl font-bold text-purple-400">Rp {{ number_format($totalAmount, 0, ',', '.') }}</h3>
    </div>
@endsection

@section('content')
<div class="flex flex-wrap justify-between items-center gap-4 mb-6">
    <h2 class="text-2xl font-bold text-white">Payment Management</h2>
    
    <!-- Filters -->
    <form method="GET" action="{{ route('payments.index') }}" class="flex flex-wrap gap-3">
        <select name="status" class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white">
            <option class="text-black" value="">All Status</option>
            @foreach($statuses as $status)
                <option class="text-black" value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                    {{ ucfirst($status) }}
                </option>
            @endforeach
        </select>
        
        <select name="payment_method" class="bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white">
            <option class="text-black" value="">All Methods</option>
            @foreach($paymentMethods as $method)
                <option class="text-black" value="{{ $method }}" {{ request('payment_method') == $method ? 'selected' : '' }}>
                    {{ $method }}
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
        
        @if(request()->hasAny(['status', 'payment_method', 'search']))
            <a href="{{ route('payments.index') }}" class="bg-gray-600 hover:bg-gray-700 px-4 py-2 rounded-lg transition">
                Reset
            </a>
        @endif
    </form>
</div>

@if(session('success'))
    <div class="bg-green-500/20 border border-green-500 text-green-300 px-4 py-3 rounded-lg mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- Payments Table -->
<div class="overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="border-b border-white/10">
                <th class="text-left py-3 px-4 text-gray-400">ID</th>
                <th class="text-left py-3 px-4 text-gray-400">Invoice</th>
                <th class="text-left py-3 px-4 text-gray-400">Customer</th>
                <th class="text-left py-3 px-4 text-gray-400">Method</th>
                <th class="text-left py-3 px-4 text-gray-400">Amount</th>
                <th class="text-left py-3 px-4 text-gray-400">Status</th>
                <th class="text-left py-3 px-4 text-gray-400">Payment Date</th>
                <th class="text-center py-3 px-4 text-gray-400">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
            <tr class="border-b border-white/10 hover:bg-white/5 transition">
                <td class="py-3 px-4 text-sm">#{{ $payment->id }}</td>
                <td class="py-3 px-4">
                    <div>
                        <p class="font-semibold text-sm">{{ $payment->order->invoice_number ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-400">Ticket: {{ $payment->order->ticket_code ?? 'N/A' }}</p>
                    </div>
                </td>
                <td class="py-3 px-4">
                    <div>
                        <p class="font-semibold">{{ $payment->order->participant->name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-400">{{ $payment->order->participant->email ?? 'N/A' }}</p>
                    </div>
                </td>
                <td class="py-3 px-4 text-sm">{{ $payment->payment_method }}</td>
                <td class="py-3 px-4">
                    <p class="font-semibold text-green-400">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                </td>
                <td class="py-3 px-4">
                    @if($payment->status == 'pending')
                        <span class="px-2 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-xs">Pending</span>
                    @elseif($payment->status == 'confirmed')
                        <span class="px-2 py-1 bg-green-500/20 text-green-300 rounded-full text-xs">Confirmed</span>
                    @else
                        <span class="px-2 py-1 bg-red-500/20 text-red-300 rounded-full text-xs">Rejected</span>
                    @endif
                </td>
                <td class="py-3 px-4 text-sm">
                    {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : '-' }}
                </td>
                <td class="py-3 px-4">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('payments.show', $payment) }}" 
                           class="text-blue-400 hover:text-blue-300 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        @if($payment->status == 'pending')
                            <a href="#" 
                               class="text-yellow-400 hover:text-yellow-300 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                        @endif
                        <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('Delete this payment record?')"
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
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    <p>No payment records found</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($payments->hasPages())
    <div class="mt-6">
        {{ $payments->appends(request()->query())->links() }}
    </div>
@endif
@endsection