@extends('layouts.app')

@section('title', 'Attendance List - ' . $event->title)
@section('page-title', 'Attendance List')
@section('page-description', 'Manage participant attendance for ' . $event->title)

@section('content')
<div class="mb-6">
    <a href="{{ route('events.show', $event) }}" class="text-gray-400 hover:text-white transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Event
    </a>
</div>

<!-- Event Info -->
<div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6 mb-6">
    <div class="flex justify-between items-start flex-wrap gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white">{{ $event->title }}</h2>
            <p class="text-gray-400 text-sm mt-1">{{ $event->start_date->format('d F Y H:i') }} - {{ $event->end_date->format('H:i') }} WIB</p>
            <p class="text-gray-400 text-sm">{{ $event->location }}</p>
        </div>
        <a href="{{ route('attendance.export', $event) }}" 
           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Export CSV
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white/5 rounded-xl border border-white/10 p-4 text-center hover:bg-white/10 transition">
        <p class="text-2xl font-bold text-blue-400">{{ $stats['total'] }}</p>
        <p class="text-xs text-gray-400">Total Registered</p>
    </div>
    <div class="bg-white/5 rounded-xl border border-white/10 p-4 text-center hover:bg-white/10 transition">
        <p class="text-2xl font-bold text-yellow-400">{{ $stats['checked_in'] }}</p>
        <p class="text-xs text-gray-400">Checked In</p>
    </div>
    <div class="bg-white/5 rounded-xl border border-white/10 p-4 text-center hover:bg-white/10 transition">
        <p class="text-2xl font-bold text-green-400">{{ $stats['checked_out'] }}</p>
        <p class="text-xs text-gray-400">Checked Out</p>
    </div>
    <div class="bg-white/5 rounded-xl border border-white/10 p-4 text-center hover:bg-white/10 transition">
        <p class="text-2xl font-bold text-gray-400">{{ $stats['pending'] }}</p>
        <p class="text-xs text-gray-400">Pending</p>
    </div>
</div>

<!-- Attendance Table -->
<div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        Attendance Records
    </h3>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-white/10">
                    <th class="text-left py-3 px-4 text-gray-400">Participant</th>
                    <th class="text-left py-3 px-4 text-gray-400">QR Code</th>
                    <th class="text-left py-3 px-4 text-gray-400">Check In</th>
                    <th class="text-left py-3 px-4 text-gray-400">Check Out</th>
                    <th class="text-left py-3 px-4 text-gray-400">Status</th>
                    <th class="text-left py-3 px-4 text-gray-400">IP Address</th>
                 </tr>
            </thead>
            <tbody>
                @forelse($attendances as $attendance)
                <tr class="border-b border-white/10 hover:bg-white/5 transition">
                    <td class="py-3 px-4">
                        <div>
                            <p class="font-semibold text-white">{{ $attendance->participant->name }}</p>
                            <p class="text-xs text-gray-400">{{ $attendance->participant->email }}</p>
                        </div>
                     </td>
                    <td class="py-3 px-4">
                        <code class="text-green-400 text-xs">{{ $attendance->qr_code }}</code>
                     </td>
                    <td class="py-3 px-4">
                        @if($attendance->check_in_time)
                            <span class="text-green-400">{{ $attendance->check_in_time->format('d M H:i:s') }}</span>
                        @else
                            <span class="text-gray-500">-</span>
                        @endif
                     </td>
                    <td class="py-3 px-4">
                        @if($attendance->check_out_time)
                            <span class="text-yellow-400">{{ $attendance->check_out_time->format('d M H:i:s') }}</span>
                        @else
                            <span class="text-gray-500">-</span>
                        @endif
                     </td>
                    <td class="py-3 px-4">
                        @if($attendance->status == 'checked_in')
                            <span class="px-2 py-1 bg-yellow-500/20 text-yellow-300 rounded-full text-xs">Checked In</span>
                        @elseif($attendance->status == 'checked_out')
                            <span class="px-2 py-1 bg-green-500/20 text-green-300 rounded-full text-xs">Checked Out</span>
                        @else
                            <span class="px-2 py-1 bg-gray-500/20 text-gray-300 rounded-full text-xs">Pending</span>
                        @endif
                     </td>
                    <td class="py-3 px-4 text-xs font-mono">
                        @if($attendance->check_in_ip)
                            {{ $attendance->check_in_ip }}
                        @else
                            -
                        @endif
                     </td>
                 </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p>No attendance records found</p>
                     </td>
                 </tr>
                @endforelse
            </tbody>
         </table>
    </div>
    
    @if($attendances->hasPages())
    <div class="mt-6">
        {{ $attendances->links() }}
    </div>
    @endif
</div>
@endsection