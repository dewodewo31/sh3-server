<?php

use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EventController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\EventGalleryController;
use App\Http\Controllers\MerchandiseController;
use App\Http\Controllers\MerchandiseOrderController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Guest Routes (Not Authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/', [UserController::class, 'login'])->name('login');
    Route::post('/auth', [UserController::class, 'auth'])
        ->middleware('throttle:5,1')
        ->name('login.auth');
});

/*
|--------------------------------------------------------------------------
| Dashboard & Logout - Semua User Login
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Admin Full Access & Admin Laman Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,admin_full_access,admin_laman'])->group(function () {
    
    Route::resource('events', EventController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('orders', OrderController::class)->except(['create', 'edit']);
    Route::patch('orders/{order}/verify-payment', [OrderController::class, 'verifyPayment'])->name('orders.verify-payment');
    Route::patch('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('orders/{order}/update-payment', [OrderController::class, 'updatePaymentProof'])->name('orders.update-payment');
    Route::get('orders/export/csv', [OrderController::class, 'export'])->name('orders.export');
    Route::get('orders/{order}/export-invoice', [OrderController::class, 'exportInvoicePdf'])->name('orders.export-invoice');
    Route::get('orders/export/all/pdf', [OrderController::class, 'exportAllPdf'])->name('orders.export-all-pdf');
    
    Route::resource('payments', PaymentController::class)->except(['create', 'edit']);
    Route::patch('payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
    
    Route::resource('galleries', EventGalleryController::class)->parameters(['galleries' => 'eventGallery']);
    Route::delete('galleries/{eventGallery}/delete-image/{imageIndex}', [EventGalleryController::class, 'deleteImage'])->name('galleries.delete-image');
    
    Route::resource('sponsors', SponsorController::class);
    Route::post('sponsors/{sponsor}/toggle-status', [SponsorController::class, 'toggleStatus'])->name('sponsors.toggle-status');
    
    Route::resource('merchandise', MerchandiseController::class);
    Route::post('merchandise/{merchandise}/update-stock', [MerchandiseController::class, 'updateStock'])->name('merchandise.update-stock');
    Route::post('merchandise/{merchandise}/toggle-status', [MerchandiseController::class, 'toggleStatus'])->name('merchandise.toggle-status');
    
    Route::get('merchandise-orders', [MerchandiseOrderController::class, 'index'])->name('merchandise.orders');
    Route::get('merchandise-orders/{order}', [MerchandiseOrderController::class, 'show'])->name('merchandise.orders.show');
    Route::post('merchandise-orders/{order}/update-status', [MerchandiseOrderController::class, 'updateStatus'])->name('merchandise.orders.update-status');
    
    Route::prefix('participants')->name('participants.')->group(function () {
        Route::get('/', [ParticipantController::class, 'index'])->name('index');
        Route::get('/create', [ParticipantController::class, 'create'])->name('create');
        Route::post('/', [ParticipantController::class, 'store'])->name('store');
        Route::get('/{participant}', [ParticipantController::class, 'show'])->name('show');
        Route::get('/{participant}/edit', [ParticipantController::class, 'edit'])->name('edit');
        Route::put('/{participant}', [ParticipantController::class, 'update'])->name('update');
        Route::delete('/{participant}', [ParticipantController::class, 'destroy'])->name('destroy');
        Route::get('/{participant}/regenerate-hash', [ParticipantController::class, 'regenerateHashId'])->name('regenerate-hash');
        Route::post('/{participant}/toggle-status', [ParticipantController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/{participant}/upgrade-to-member', [ParticipantController::class, 'upgradeToMember'])->name('upgrade-to-member');
        Route::get('/export/csv', [ParticipantController::class, 'export'])->name('export');
        Route::get('/search', [ParticipantController::class, 'search'])->name('search');
        Route::get('/{participant}/export-pdf', [ParticipantController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export/all/pdf', [ParticipantController::class, 'exportAllPdf'])->name('export-all-pdf');
        Route::post('/{participant}/issue-warning', [ParticipantController::class, 'issueWarning'])->name('issue-warning');
        Route::get('/{participant}/warnings', [ParticipantController::class, 'getWarnings'])->name('warnings');
        Route::delete('/{participant}/warnings/{warning}', [ParticipantController::class, 'removeWarning'])->name('remove-warning');
        Route::get('/{participant}/check-can-join', [ParticipantController::class, 'checkCanJoinEvent'])->name('check-can-join');
    });
    
    Route::resource('users', UserController::class);
    
    Route::get('attendance/{eventId}/qrcode', [AttendanceController::class, 'showQrCode'])->name('attendance.qrcode');
    Route::get('attendance/scanner', [AttendanceController::class, 'scanner'])->name('attendance.scanner');
    Route::get('attendance/scan/{qrCode}', [AttendanceController::class, 'scan'])->name('attendance.scan');
    Route::get('attendance/status/{qrCode}', [AttendanceController::class, 'getStatus'])->name('attendance.status');
    Route::get('events/{event}/attendance', [AttendanceController::class, 'eventAttendance'])->name('attendance.event-list');
    Route::get('events/{event}/attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');
    Route::get('events/{event}/export-brochure', [EventController::class, 'exportBrochurePdf'])->name('events.export-brochure');
    Route::get('events/export/all/pdf', [EventController::class, 'exportAllPdf'])->name('events.export-all-pdf');
});

/*
|--------------------------------------------------------------------------
| Organizer Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:organizer,admin_full_access'])->group(function () {
    Route::resource('events', EventController::class);
    Route::resource('orders', OrderController::class)->except(['create', 'edit']);
    Route::patch('orders/{order}/verify-payment', [OrderController::class, 'verifyPayment'])->name('orders.verify-payment');
    Route::patch('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('orders/{order}/update-payment', [OrderController::class, 'updatePaymentProof'])->name('orders.update-payment');
    Route::get('orders/export/csv', [OrderController::class, 'export'])->name('orders.export');
    Route::get('orders/{order}/export-invoice', [OrderController::class, 'exportInvoicePdf'])->name('orders.export-invoice');
    Route::get('orders/export/all/pdf', [OrderController::class, 'exportAllPdf'])->name('orders.export-all-pdf');
    Route::resource('payments', PaymentController::class)->except(['create', 'edit']);
    Route::patch('payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
    Route::resource('galleries', EventGalleryController::class)->parameters(['galleries' => 'eventGallery']);
    Route::delete('galleries/{eventGallery}/delete-image/{imageIndex}', [EventGalleryController::class, 'deleteImage'])->name('galleries.delete-image');
    Route::get('events/{event}/export-brochure', [EventController::class, 'exportBrochurePdf'])->name('events.export-brochure');
    Route::get('events/export/all/pdf', [EventController::class, 'exportAllPdf'])->name('events.export-all-pdf');
    Route::get('merchandise', [MerchandiseController::class, 'index'])->name('merchandise.index');
    Route::get('merchandise/{merchandise}', [MerchandiseController::class, 'show'])->name('merchandise.show');
    Route::get('attendance/{eventId}/qrcode', [AttendanceController::class, 'showQrCode'])->name('attendance.qrcode');
    Route::get('attendance/scanner', [AttendanceController::class, 'scanner'])->name('attendance.scanner');
    Route::get('attendance/scan/{qrCode}', [AttendanceController::class, 'scan'])->name('attendance.scan');
    Route::get('attendance/status/{qrCode}', [AttendanceController::class, 'getStatus'])->name('attendance.status');
    Route::get('events/{event}/attendance', [AttendanceController::class, 'eventAttendance'])->name('attendance.event-list');
    Route::get('events/{event}/attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');
});

/*
|--------------------------------------------------------------------------
| Bendahara Routes (Dashboard, Orders, Payments)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:bendahara,admin_full_access'])->group(function () {
    Route::resource('orders', OrderController::class)->except(['create', 'edit']);
    Route::patch('orders/{order}/verify-payment', [OrderController::class, 'verifyPayment'])->name('orders.verify-payment');
    Route::patch('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('orders/{order}/update-payment', [OrderController::class, 'updatePaymentProof'])->name('orders.update-payment');
    Route::get('orders/export/csv', [OrderController::class, 'export'])->name('orders.export');
    Route::get('orders/{order}/export-invoice', [OrderController::class, 'exportInvoicePdf'])->name('orders.export-invoice');
    Route::get('orders/export/all/pdf', [OrderController::class, 'exportAllPdf'])->name('orders.export-all-pdf');
    Route::resource('payments', PaymentController::class)->except(['create', 'edit']);
    Route::patch('payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
});

/*
|--------------------------------------------------------------------------
| Admin Member Routes (Participants only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin_member,admin_full_access'])->group(function () {
    Route::prefix('participants')->name('participants.')->group(function () {
        Route::get('/', [ParticipantController::class, 'index'])->name('index');
        Route::get('/create', [ParticipantController::class, 'create'])->name('create');
        Route::post('/', [ParticipantController::class, 'store'])->name('store');
        Route::get('/{participant}', [ParticipantController::class, 'show'])->name('show');
        Route::get('/{participant}/edit', [ParticipantController::class, 'edit'])->name('edit');
        Route::put('/{participant}', [ParticipantController::class, 'update'])->name('update');
        Route::delete('/{participant}', [ParticipantController::class, 'destroy'])->name('destroy');
        Route::get('/{participant}/regenerate-hash', [ParticipantController::class, 'regenerateHashId'])->name('regenerate-hash');
        Route::post('/{participant}/toggle-status', [ParticipantController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/{participant}/upgrade-to-member', [ParticipantController::class, 'upgradeToMember'])->name('upgrade-to-member');
        Route::get('/export/csv', [ParticipantController::class, 'export'])->name('export');
        Route::get('/search', [ParticipantController::class, 'search'])->name('search');
        Route::get('/{participant}/export-pdf', [ParticipantController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/export/all/pdf', [ParticipantController::class, 'exportAllPdf'])->name('export-all-pdf');
        Route::post('/{participant}/issue-warning', [ParticipantController::class, 'issueWarning'])->name('issue-warning');
        Route::get('/{participant}/warnings', [ParticipantController::class, 'getWarnings'])->name('warnings');
        Route::delete('/{participant}/warnings/{warning}', [ParticipantController::class, 'removeWarning'])->name('remove-warning');
        Route::get('/{participant}/check-can-join', [ParticipantController::class, 'checkCanJoinEvent'])->name('check-can-join');
    });
});

/*
|--------------------------------------------------------------------------
| Admin BNH Routes (Gallery only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin_bnh,admin_full_access'])->group(function () {
    Route::resource('galleries', EventGalleryController::class)->parameters(['galleries' => 'eventGallery']);
    Route::delete('galleries/{eventGallery}/delete-image/{imageIndex}', [EventGalleryController::class, 'deleteImage'])->name('galleries.delete-image');
});

/*
|--------------------------------------------------------------------------
| Sponsor Routes (Sponsors only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:sponsor,admin_full_access'])->group(function () {
    Route::resource('sponsors', SponsorController::class);
    Route::post('sponsors/{sponsor}/toggle-status', [SponsorController::class, 'toggleStatus'])->name('sponsors.toggle-status');
});

/*
|--------------------------------------------------------------------------
| Merchandise Routes (Merchandise only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:merchandise,admin_full_access'])->group(function () {
    Route::resource('merchandise', MerchandiseController::class);
    Route::post('merchandise/{merchandise}/update-stock', [MerchandiseController::class, 'updateStock'])->name('merchandise.update-stock');
    Route::post('merchandise/{merchandise}/toggle-status', [MerchandiseController::class, 'toggleStatus'])->name('merchandise.toggle-status');
    Route::get('merchandise-orders', [MerchandiseOrderController::class, 'index'])->name('merchandise.orders');
    Route::get('merchandise-orders/{order}', [MerchandiseOrderController::class, 'show'])->name('merchandise.orders.show');
    Route::post('merchandise-orders/{order}/update-status', [MerchandiseOrderController::class, 'updateStatus'])->name('merchandise.orders.update-status');
});

/*
|--------------------------------------------------------------------------
| Participant Routes (Default - No Access)
|--------------------------------------------------------------------------
*/
// Participant tidak memiliki akses ke admin panel
// Mereka hanya bisa login via API