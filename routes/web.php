<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EventController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\EventGalleryController;
use App\Http\Controllers\ParticipantController;
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
| Admin & Organizer Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,organizer'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    
    // Events - Full CRUD
    Route::resource('events', EventController::class);
    
    // Categories - Full CRUD
    Route::resource('categories', CategoryController::class);
    
    // Orders - No create/edit forms
    Route::resource('orders', OrderController::class)->except(['create', 'edit']);
    
    // Order Additional Routes
    Route::patch('orders/{order}/verify-payment', [OrderController::class, 'verifyPayment'])->name('orders.verify-payment');
    Route::patch('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('orders/{order}/update-payment', [OrderController::class, 'updatePaymentProof'])->name('orders.update-payment');
    Route::get('orders/export/csv', [OrderController::class, 'export'])->name('orders.export');
    
    // Order Export PDF
    Route::get('orders/{order}/export-invoice', [OrderController::class, 'exportInvoicePdf'])->name('orders.export-invoice');
    Route::get('orders/export/all/pdf', [OrderController::class, 'exportAllPdf'])->name('orders.export-all-pdf');
    
    // Payments
    Route::resource('payments', PaymentController::class)->except(['create', 'edit']);
    Route::patch('payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
    
    // Galleries
    Route::resource('galleries', EventGalleryController::class)
        ->parameters(['galleries' => 'eventGallery']);
    
    Route::delete('galleries/{eventGallery}/delete-image/{imageIndex}', [EventGalleryController::class, 'deleteImage'])
        ->name('galleries.delete-image');
    
    // Event Export PDF
    Route::get('events/{event}/export-brochure', [EventController::class, 'exportBrochurePdf'])->name('events.export-brochure');
    Route::get('events/export/all/pdf', [EventController::class, 'exportAllPdf'])->name('events.export-all-pdf');
    
    // Logout
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Admin Only Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->group(function () {
    
    // Users Management
    Route::resource('users', UserController::class);
    
    // Participants Management
    Route::resource('participants', ParticipantController::class);
    
    // Participant Additional Routes
    Route::get('participants/{participant}/regenerate-hash', [ParticipantController::class, 'regenerateHashId'])
        ->name('participants.regenerate-hash');
    
    Route::post('participants/{participant}/toggle-status', [ParticipantController::class, 'toggleStatus'])
        ->name('participants.toggle-status');
    
    Route::get('participants/export/csv', [ParticipantController::class, 'export'])
        ->name('participants.export');
    
    Route::get('participants/search', [ParticipantController::class, 'search'])
        ->name('participants.search');
    
    // Participant Export PDF (Admin Only)
    Route::get('participants/{participant}/export-pdf', [ParticipantController::class, 'exportPdf'])
        ->name('participants.export-pdf');
    
    Route::get('participants/export/all/pdf', [ParticipantController::class, 'exportAllPdf'])
        ->name('participants.export-all-pdf');
});