@extends('layouts.app')

@section('title', 'Event QR Code - ' . $event->title)
@section('page-title', 'Event QR Code')
@section('page-description', 'Scan this QR code for attendance')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-8 text-center">
        <div class="mb-4">
            <h2 class="text-2xl font-bold text-white">{{ $event->title }}</h2>
            <p class="text-gray-400 text-sm mt-1">{{ $event->start_date->format('d F Y H:i') }} - {{ $event->end_date->format('H:i') }} WIB</p>
        </div>
        
        <div class="bg-white p-4 rounded-xl inline-block mb-4">
            {!! QrCode::size(200)->generate($qrCodeUrl) !!}
        </div>
        
        <div class="mb-4">
            <p class="text-gray-300">Your QR Code</p>
            <code class="text-green-400 text-sm">{{ $attendance->qr_code }}</code>
        </div>
        
        <div class="text-left bg-white/5 rounded-lg p-4 mb-4">
            <h3 class="font-semibold text-white mb-2">Attendance Status</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-400">Status:</span>
                    <span class="
                        @if($attendance->status == 'checked_in') text-yellow-400
                        @elseif($attendance->status == 'checked_out') text-green-400
                        @else text-gray-400 @endif">
                        {{ ucfirst($attendance->status) }}
                    </span>
                </div>
                @if($attendance->check_in_time)
                <div class="flex justify-between">
                    <span class="text-gray-400">Check In:</span>
                    <span>{{ $attendance->check_in_time->format('H:i:s') }}</span>
                </div>
                @endif
                @if($attendance->check_out_time)
                <div class="flex justify-between">
                    <span class="text-gray-400">Check Out:</span>
                    <span>{{ $attendance->check_out_time->format('H:i:s') }}</span>
                </div>
                @endif
            </div>
        </div>
        
        <div class="bg-blue-500/10 border border-blue-500/30 rounded-lg p-3">
            <p class="text-xs text-gray-300">
                📌 Present your QR code to the event organizer for scanning.
                <br>Check out can only be done 15 minutes after check in.
            </p>
        </div>
        
        <div class="mt-6">
            <a href="{{ route('events.show', $event) }}" class="text-gray-400 hover:text-white transition">
                ← Back to Event
            </a>
        </div>
    </div>
</div>
@endsection