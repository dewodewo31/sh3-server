@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')
@section('page-description', 'Overview of all platform activities')

@section('content')
<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-br from-blue-500/10 to-blue-600/10 rounded-xl border border-blue-500/30 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm">Total Events</p>
                <p class="text-3xl font-bold text-blue-400">{{ number_format($totalEvents) }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-green-500/10 to-green-600/10 rounded-xl border border-green-500/30 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm">Total Participants</p>
                <p class="text-3xl font-bold text-green-400">{{ number_format($totalParticipants) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-purple-500/10 to-purple-600/10 rounded-xl border border-purple-500/30 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm">Total Orders</p>
                <p class="text-3xl font-bold text-purple-400">{{ number_format($totalOrders) }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-yellow-500/10 to-yellow-600/10 rounded-xl border border-yellow-500/30 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm">Total Revenue</p>
                <p class="text-3xl font-bold text-yellow-400">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- CHARTS SECTION -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Participant Registration Chart -->
    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Participant Registration Trend
            </h3>
            <div class="text-xs text-gray-400">Last 6 months</div>
        </div>
        <canvas id="participantChart" height="250"></canvas>
    </div>

    <!-- Revenue Chart -->
    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Revenue Trend
            </h3>
            <div class="text-xs text-gray-400">Last 6 months</div>
        </div>
        <canvas id="revenueChart" height="250"></canvas>
    </div>
</div>

<!-- Order Status Chart & Top Events -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Order Status Distribution -->
    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Order Status Distribution
        </h3>
        <canvas id="orderStatusChart" height="250"></canvas>
        <div class="flex justify-center gap-4 mt-4 text-xs">
            <div class="flex items-center gap-1"><span class="w-3 h-3 bg-yellow-500 rounded-full"></span><span class="text-gray-400">Pending</span></div>
            <div class="flex items-center gap-1"><span class="w-3 h-3 bg-green-500 rounded-full"></span><span class="text-gray-400">Paid</span></div>
            <div class="flex items-center gap-1"><span class="w-3 h-3 bg-blue-500 rounded-full"></span><span class="text-gray-400">Free</span></div>
            <div class="flex items-center gap-1"><span class="w-3 h-3 bg-red-500 rounded-full"></span><span class="text-gray-400">Cancelled</span></div>
        </div>
    </div>

    <!-- Top Events (Most Registered) -->
    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            Top Events (Most Registered)
        </h3>
        
        @foreach($topEvents as $index => $event)
        <div class="flex justify-between items-center py-2 border-b border-white/10">
            <div class="flex items-center gap-3">
                <span class="text-2xl font-bold text-gray-500 w-8">#{{ $index + 1 }}</span>
                <div>
                    <p class="text-white">{{ $event->title }}</p>
                    <p class="text-xs text-gray-400">Quota: {{ number_format($event->quota) }}</p>
                </div>
            </div>
            <span class="text-green-400 font-semibold">{{ number_format($event->registered) }} peserta</span>
        </div>
        @endforeach
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Recent Orders -->
    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            Recent Orders
        </h3>
        
        @foreach($recentOrders as $order)
        <div class="flex justify-between items-center py-2 border-b border-white/10">
            <div>
                <p class="text-white text-sm">{{ $order->participant_name }}</p>
                <p class="text-xs text-gray-400">{{ $order->event_title }}</p>
            </div>
            <div class="text-right">
                <p class="text-green-400 font-semibold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                <span class="px-2 py-1 text-xs rounded-full 
                    @if($order->status == 'pending') bg-yellow-500/20 text-yellow-300
                    @elseif($order->status == 'paid') bg-green-500/20 text-green-300
                    @else bg-red-500/20 text-red-300 @endif">
                    {{ ucfirst($order->status) }}
                </span>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Pending Payments -->
    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
        <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Pending Payments
        </h3>
        
        @forelse($pendingPayments as $payment)
        <div class="flex justify-between items-center py-2 border-b border-white/10">
            <div>
                <p class="text-white text-sm">{{ $payment->participant_name }}</p>
                <p class="text-xs text-gray-400">{{ $payment->event_title }}</p>
                <p class="text-xs text-gray-500">{{ $payment->invoice_number }}</p>
            </div>
            <div class="text-right">
                <p class="text-yellow-400 font-semibold">Rp {{ number_format($payment->amount, 0, ',', '.') }}</p>
                <a href="{{ route('orders.show', $payment->order_id) }}" class="text-blue-400 hover:text-blue-300 text-xs">Verifikasi</a>
            </div>
        </div>
        @empty
        <p class="text-gray-400 text-center py-4">Tidak ada pembayaran pending</p>
        @endforelse
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Participant Registration Chart (Line Chart)
        const participantCtx = document.getElementById('participantChart').getContext('2d');
        new Chart(participantCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($participantChart, 'month')) !!},
                datasets: [{
                    label: 'New Participants',
                    data: {!! json_encode(array_column($participantChart, 'count')) !!},
                    borderColor: '#4CAF50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#4CAF50',
                    pointBorderColor: '#fff',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        labels: { color: '#fff' }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Participants: ${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255,255,255,0.1)' },
                        ticks: { color: '#fff', stepSize: 1 }
                    },
                    x: {
                        grid: { color: 'rgba(255,255,255,0.1)' },
                        ticks: { color: '#fff' }
                    }
                }
            }
        });

        // Revenue Chart (Bar Chart)
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_column($revenueChart, 'month')) !!},
                datasets: [{
                    label: 'Revenue (Rp)',
                    data: {!! json_encode(array_column($revenueChart, 'revenue')) !!},
                    backgroundColor: 'rgba(255, 193, 7, 0.7)',
                    borderColor: '#FFC107',
                    borderWidth: 1,
                    borderRadius: 8,
                    barPercentage: 0.65
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        labels: { color: '#fff' }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Revenue: Rp ${context.raw.toLocaleString('id-ID')}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255,255,255,0.1)' },
                        ticks: { 
                            color: '#fff',
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    },
                    x: {
                        grid: { color: 'rgba(255,255,255,0.1)' },
                        ticks: { color: '#fff' }
                    }
                }
            }
        });

        // Order Status Distribution (Doughnut Chart)
        const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
        new Chart(orderStatusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Paid', 'Free', 'Cancelled'],
                datasets: [{
                    data: [
                        {{ $pendingOrders ?? 0 }},
                        {{ $paidOrders ?? 0 }},
                        {{ $freeOrders ?? 0 }},
                        {{ $cancelledOrders ?? 0 }}
                    ],
                    backgroundColor: [
                        '#FFC107', // Pending - Yellow
                        '#4CAF50', // Paid - Green
                        '#2196F3', // Free - Blue
                        '#F44336'  // Cancelled - Red
                    ],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: '#fff', font: { size: 11 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.raw / total) * 100).toFixed(1);
                                return `${context.label}: ${context.raw} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection