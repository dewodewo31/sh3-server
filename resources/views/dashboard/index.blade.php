@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')
@section('page-description', 'Overview of all platform activities')

@section('content')
<!-- Statistics Cards - Smaller Version -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
    <div class="bg-gradient-to-br from-blue-500/10 to-blue-600/10 rounded-lg border border-blue-500/30 p-3">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-xs">Total Events</p>
                <p class="text-xl font-bold text-blue-400">{{ number_format($totalEvents ?? 0) }}</p>
            </div>
            <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-green-500/10 to-green-600/10 rounded-lg border border-green-500/30 p-3">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-xs">Total Participants</p>
                <p class="text-xl font-bold text-green-400">{{ number_format($totalParticipants ?? 0) }}</p>
            </div>
            <div class="w-8 h-8 bg-green-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-purple-500/10 to-purple-600/10 rounded-lg border border-purple-500/30 p-3">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-xs">Total Orders</p>
                <p class="text-xl font-bold text-purple-400">{{ number_format($totalOrders ?? 0) }}</p>
            </div>
            <div class="w-8 h-8 bg-purple-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-yellow-500/10 to-yellow-600/10 rounded-lg border border-yellow-500/30 p-3">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-xs">Total Revenue</p>
                <p class="text-sm font-bold text-yellow-400">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="w-8 h-8 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- CHARTS SECTION - Smaller -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
    <!-- Participant Registration Chart -->
    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-lg border border-white/10 p-3">
        <div class="flex justify-between items-center mb-2">
            <h3 class="text-sm font-semibold text-white flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Participant Registration Trend
            </h3>
            <div class="text-xs text-gray-400">Last 6 months</div>
        </div>
        <canvas id="participantChart" height="140"></canvas>
    </div>

    <!-- Revenue Chart -->
    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-lg border border-white/10 p-3">
        <div class="flex justify-between items-center mb-2">
            <h3 class="text-sm font-semibold text-white flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Revenue Trend
            </h3>
            <div class="text-xs text-gray-400">Last 6 months</div>
        </div>
        <canvas id="revenueChart" height="140"></canvas>
    </div>
</div>

<!-- Order Status Chart & Top Events - Smaller -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
    <!-- Order Status Distribution -->
    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-lg border border-white/10 p-3">
        <h3 class="text-sm font-semibold text-white mb-2 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Order Status Distribution
        </h3>
        <canvas id="orderStatusChart" height="140"></canvas>
        <div class="flex justify-center gap-2 mt-2 text-xs">
            <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 bg-yellow-500 rounded-full"></span><span class="text-gray-400">Pending</span></div>
            <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span><span class="text-gray-400">Paid</span></div>
            <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 bg-blue-500 rounded-full"></span><span class="text-gray-400">Free</span></div>
            <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 bg-red-500 rounded-full"></span><span class="text-gray-400">Cancelled</span></div>
        </div>
    </div>

    <!-- Top Events (Most Registered) -->
    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-lg border border-white/10 p-3">
        <h3 class="text-sm font-semibold text-white mb-2 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            Top Events (Most Registered)
        </h3>
        
        @php $topEvents = $topEvents ?? collect(); @endphp
        @if($topEvents->count() > 0)
            @foreach($topEvents as $index => $event)
            <div class="flex justify-between items-center py-1 border-b border-white/10">
                <div class="flex items-center gap-1.5">
                    <span class="text-base font-bold text-gray-500 w-5">#{{ $index + 1 }}</span>
                    <div>
                        <p class="text-white text-xs">{{ Str::limit($event['title'] ?? 'N/A', 30) }}</p>
                        <p class="text-xs text-gray-400">Quota: {{ number_format($event['quota'] ?? 0) }}</p>
                    </div>
                </div>
                <span class="text-green-400 font-semibold text-xs">{{ number_format($event['registered'] ?? 0) }} peserta</span>
            </div>
            @endforeach
        @else
            <div class="text-center py-6">
                <svg class="w-12 h-12 mx-auto text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-400 text-sm">Belum ada event</p>
                <p class="text-xs text-gray-500">Buat event pertama Anda</p>
                <a href="{{ route('events.create') }}" class="mt-2 inline-block text-green-400 hover:text-green-300 text-sm">
                    + Buat Event
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Recent Orders & Pending Payments - Smaller -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <!-- Recent Orders -->
    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-lg border border-white/10 p-3">
        <h3 class="text-sm font-semibold text-white mb-2 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            Recent Orders
        </h3>
        
        @php $recentOrders = $recentOrders ?? collect(); @endphp
        @if($recentOrders->count() > 0)
            <div class="space-y-1">
                @foreach($recentOrders as $order)
                <div class="flex justify-between items-center py-1 border-b border-white/10">
                    <div>
                        <p class="text-white text-xs">{{ Str::limit($order['participant_name'] ?? 'N/A', 25) }}</p>
                        <p class="text-xs text-gray-400">{{ Str::limit($order['event_title'] ?? 'N/A', 30) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-green-400 font-semibold text-xs">Rp {{ number_format($order['total_price'] ?? 0, 0, ',', '.') }}</p>
                        <span class="px-1.5 py-0.5 text-xs rounded-full 
                            @if(($order['status'] ?? '') == 'pending') bg-yellow-500/20 text-yellow-300
                            @elseif(($order['status'] ?? '') == 'paid') bg-green-500/20 text-green-300
                            @else bg-red-500/20 text-red-300 @endif">
                            {{ ucfirst($order['status'] ?? 'Unknown') }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-6">
                <svg class="w-12 h-12 mx-auto text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                <p class="text-gray-400 text-sm">Belum ada order</p>
                <p class="text-xs text-gray-500">Order akan muncul di sini</p>
            </div>
        @endif
    </div>
    
    <!-- Pending Payments -->
    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-lg border border-white/10 p-3">
        <h3 class="text-sm font-semibold text-white mb-2 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Pending Payments
        </h3>
        
        @php $pendingPayments = $pendingPayments ?? collect(); @endphp
        @if($pendingPayments->count() > 0)
            <div class="space-y-1">
                @foreach($pendingPayments as $payment)
                <div class="flex justify-between items-center py-1 border-b border-white/10">
                    <div>
                        <p class="text-white text-xs">{{ Str::limit($payment['participant_name'] ?? 'N/A', 25) }}</p>
                        <p class="text-xs text-gray-400">{{ Str::limit($payment['event_title'] ?? 'N/A', 30) }}</p>
                        <p class="text-xs text-gray-500">{{ $payment['invoice_number'] ?? 'N/A' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-yellow-400 font-semibold text-xs">Rp {{ number_format($payment['amount'] ?? 0, 0, ',', '.') }}</p>
                        <a href="{{ route('orders.show', $payment['order_id'] ?? 0) }}" class="text-blue-400 hover:text-blue-300 text-xs">Verifikasi</a>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-6">
                <svg class="w-12 h-12 mx-auto text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-400 text-sm">Tidak ada pembayaran pending</p>
                <p class="text-xs text-gray-500">Semua pembayaran sudah terverifikasi</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Participant Registration Chart
        const participantData = @json($participantChart ?? []);
        const participantCtx = document.getElementById('participantChart');
        if (participantCtx && participantData && participantData.length > 0) {
            new Chart(participantCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: participantData.map(item => item.month),
                    datasets: [{
                        label: 'New Participants',
                        data: participantData.map(item => item.count),
                        borderColor: '#4CAF50',
                        backgroundColor: 'rgba(76, 175, 80, 0.1)',
                        borderWidth: 1.5,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#4CAF50',
                        pointBorderColor: '#fff',
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            labels: { color: '#fff', font: { size: 10 } }
                        },
                        tooltip: {
                            titleFont: { size: 11 },
                            bodyFont: { size: 10 }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(255,255,255,0.1)' },
                            ticks: { color: '#fff', stepSize: 1, font: { size: 9 } }
                        },
                        x: {
                            grid: { color: 'rgba(255,255,255,0.1)' },
                            ticks: { color: '#fff', font: { size: 9 } }
                        }
                    }
                }
            });
        } else if (participantCtx) {
            // Tampilkan pesan jika tidak ada data
            const ctx = participantCtx.getContext('2d');
            ctx.fillStyle = '#666';
            ctx.font = '14px sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('Belum ada data participant', participantCtx.width/2, participantCtx.height/2);
        }

        // Revenue Chart
        const revenueData = @json($revenueChart ?? []);
        const revenueCtx = document.getElementById('revenueChart');
        if (revenueCtx && revenueData && revenueData.length > 0) {
            new Chart(revenueCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: revenueData.map(item => item.month),
                    datasets: [{
                        label: 'Revenue (Rp)',
                        data: revenueData.map(item => item.revenue),
                        backgroundColor: 'rgba(255, 193, 7, 0.7)',
                        borderColor: '#FFC107',
                        borderWidth: 1,
                        borderRadius: 6,
                        barPercentage: 0.7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            labels: { color: '#fff', font: { size: 10 } }
                        },
                        tooltip: {
                            titleFont: { size: 11 },
                            bodyFont: { size: 10 },
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
                                font: { size: 9 },
                                callback: function(value) {
                                    if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'M';
                                    if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'K';
                                    return 'Rp ' + value;
                                }
                            }
                        },
                        x: {
                            grid: { color: 'rgba(255,255,255,0.1)' },
                            ticks: { color: '#fff', font: { size: 9 } }
                        }
                    }
                }
            });
        } else if (revenueCtx) {
            const ctx = revenueCtx.getContext('2d');
            ctx.fillStyle = '#666';
            ctx.font = '14px sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('Belum ada data revenue', revenueCtx.width/2, revenueCtx.height/2);
        }

        // Order Status Distribution Chart
        const orderStatusCtx = document.getElementById('orderStatusChart');
        if (orderStatusCtx) {
            const totalOrders = {{ ($pendingOrders ?? 0) + ($paidOrders ?? 0) + ($freeOrders ?? 0) + ($cancelledOrders ?? 0) }};
            if (totalOrders > 0) {
                new Chart(orderStatusCtx.getContext('2d'), {
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
                            backgroundColor: ['#FFC107', '#4CAF50', '#2196F3', '#F44336'],
                            borderWidth: 0,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: '#fff', font: { size: 9 }, boxWidth: 10 }
                            },
                            tooltip: {
                                titleFont: { size: 11 },
                                bodyFont: { size: 10 },
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : 0;
                                        return `${context.label}: ${context.raw} (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                const ctx = orderStatusCtx.getContext('2d');
                ctx.fillStyle = '#666';
                ctx.font = '14px sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('Belum ada data order', orderStatusCtx.width/2, orderStatusCtx.height/2);
            }
        }
    });
</script>
@endpush
@endsection