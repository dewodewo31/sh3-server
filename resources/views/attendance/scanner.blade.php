@extends('layouts.app')

@section('title', 'QR Code Scanner')
@section('page-title', 'Attendance Scanner')
@section('page-description', 'Scan participant QR code for check in/out')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-gradient-to-br from-white/5 to-white/10 rounded-xl border border-white/10 p-6">
        <div class="text-center mb-6">
            <h3 class="text-xl font-bold text-white">Camera Scanner</h3>
            <p class="text-gray-400 text-sm">Position the QR code in front of the camera</p>
        </div>
        
        <!-- Video Container -->
        <div id="reader" class="w-full rounded-lg overflow-hidden bg-black/50" style="height: 400px;"></div>
        
        <!-- Manual Input -->
        <div class="mt-6">
            <div class="relative">
                <input type="text" 
                       id="qrCodeInput" 
                       placeholder="Or enter QR code manually"
                       class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500">
                <button onclick="submitQRCode()" 
                        class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-green-500 hover:bg-green-600 px-4 py-1 rounded-lg transition">
                    Submit
                </button>
            </div>
        </div>
        
        <!-- Result Modal -->
        <div id="resultModal" class="fixed inset-0 bg-black/90 z-50 hidden items-center justify-center p-4">
            <div class="bg-gradient-to-br from-white/10 to-white/5 rounded-xl border border-white/20 max-w-md w-full p-6">
                <div id="resultIcon" class="text-center text-6xl mb-4"></div>
                <h3 id="resultTitle" class="text-xl font-bold text-center text-white mb-2"></h3>
                <p id="resultMessage" class="text-center text-gray-300 mb-4"></p>
                <div id="resultDetails" class="bg-white/5 rounded-lg p-3 mb-4 text-sm"></div>
                <button onclick="closeModal()" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let html5QrCode;
    
    function onScanSuccess(decodedText, decodedResult) {
        // Stop scanning after success
        if (html5QrCode) {
            html5QrCode.stop();
        }
        
        // Process QR code
        processQRCode(decodedText);
    }
    
    function onScanError(errorMessage) {
        // Handle scan error (ignore, just continue scanning)
        console.log(errorMessage);
    }
    
    function startScanner() {
        html5QrCode = new Html5Qrcode("reader");
        const config = { fps: 10, qrbox: { width: 250, height: 250 } };
        
        html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess, onScanError);
    }
    
    function processQRCode(qrCode) {
        // Send to server
        fetch(`/attendance/scan/${qrCode}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            showModal(data.success, data.message, data.data);
        })
        .catch(error => {
            showModal(false, 'Error processing QR code', null);
        });
    }
    
    function submitQRCode() {
        const input = document.getElementById('qrCodeInput');
        const qrCode = input.value.trim();
        
        if (qrCode) {
            processQRCode(qrCode);
            input.value = '';
        } else {
            showModal(false, 'Please enter a valid QR code', null);
        }
    }
    
    function showModal(success, message, data) {
        const modal = document.getElementById('resultModal');
        const icon = document.getElementById('resultIcon');
        const title = document.getElementById('resultTitle');
        const msg = document.getElementById('resultMessage');
        const details = document.getElementById('resultDetails');
        
        if (success) {
            icon.innerHTML = '✅';
            title.textContent = 'Success';
            title.classList.add('text-green-400');
        } else {
            icon.innerHTML = '❌';
            title.textContent = 'Failed';
            title.classList.add('text-red-400');
        }
        
        msg.textContent = message;
        
        if (data) {
            details.innerHTML = `
                <div class="grid grid-cols-2 gap-2">
                    <div class="text-gray-400">Participant:</div>
                    <div class="text-white">${data.participant_name}</div>
                    <div class="text-gray-400">Event:</div>
                    <div class="text-white">${data.event_name}</div>
                    <div class="text-gray-400">Status:</div>
                    <div class="text-white capitalize">${data.status}</div>
                    ${data.check_in_time ? `<div class="text-gray-400">Check In:</div><div class="text-white">${new Date(data.check_in_time).toLocaleTimeString()}</div>` : ''}
                    ${data.check_out_time ? `<div class="text-gray-400">Check Out:</div><div class="text-white">${new Date(data.check_out_time).toLocaleTimeString()}</div>` : ''}
                    ${!data.can_check_out && data.status === 'checked_in' ? `<div class="text-gray-400 col-span-2 text-yellow-400">⚠️ Must wait ${data.remaining_minutes} more minutes before check out</div>` : ''}
                </div>
            `;
        } else {
            details.innerHTML = '';
        }
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        // Restart scanner after modal closed
        const closeBtn = document.querySelector('#resultModal button');
        closeBtn.onclick = () => {
            closeModal();
            startScanner();
        };
    }
    
    function closeModal() {
        const modal = document.getElementById('resultModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    
    // Start scanner when page loads
    document.addEventListener('DOMContentLoaded', function() {
        startScanner();
    });
</script>
@endpush
@endsection