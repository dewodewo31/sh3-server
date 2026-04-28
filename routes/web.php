<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EventController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\EventGalleryController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\UserController;

Route::middleware('guest')->group(function () {
    Route::get('/', [UserController::class, 'login'])->name('login');
    Route::post('/auth', [UserController::class, 'auth'])
        ->middleware('throttle:5,1')
        ->name('auth');
});

Route::middleware(['auth','role:admin,organizer'])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard.index');
    
    Route::resource('events', EventController::class, [
        'names' => [
            'index' => 'events.index',
            'create' => 'events.create',
            'store' => 'events.store',
            'show' => 'events.show',
            'edit' => 'events.edit',
            'update' => 'events.update',
            'destroy' => 'events.destroy',
        ]
    ]);

    Route::resource('categories', CategoryController::class, [
        'names' => [
            'index' => 'categories.index',
            'create' => 'categories.create',
            'store' => 'categories.store',
            'show' => 'categories.show',
            'edit' => 'categories.edit',
            'update' => 'categories.update',
            'destroy' => 'categories.destroy',
        ]
    ]);

    Route::resource('orders', OrderController::class, [
        'names' => [
            'index' => 'orders.index',
            'store' => 'orders.store',
            'show' => 'orders.show',
            'update' => 'orders.update',
            'destroy' => 'orders.destroy',
        ]
    ])->except(['create','edit']);
    Route::patch('orders/{order}/verify-payment', [OrderController::class, 'verifyPayment'])->name('orders.verify-payment');
    Route::patch('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');  // ← Tambahkan ini
    Route::post('orders/{order}/update-payment', [OrderController::class, 'updatePaymentProof'])->name('orders.update-payment');
    Route::get('orders/export/csv', [OrderController::class, 'export'])->name('orders.export');

    Route::resource('payments', PaymentController::class, [
        'names' => [
            'index' => 'payments.index',
            'store' => 'payments.store',
            'show' => 'payments.show',
            'update' => 'payments.update',
            'destroy' => 'payments.destroy',
        ]
    ])->except(['create','edit']);

    Route::resource('galleries', EventGalleryController::class, [
        'names' => [
            'index' => 'galleries.index',
            'create' => 'galleries.create',
            'store' => 'galleries.store',
            'show' => 'galleries.show',
            'edit' => 'galleries.edit',
            'update' => 'galleries.update',
            'destroy' => 'galleries.destroy',
        ]
    ])
    ->parameters([
        'galleries' => 'eventGallery' // Ubah parameter name dari 'gallery' menjadi 'eventGallery'
    ]);

    // Tambahkan route untuk delete image
    Route::delete('galleries/{eventGallery}/delete-image/{imageIndex}', [EventGalleryController::class, 'deleteImage'])
        ->name('galleries.delete-image');

    
    
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});
Route::middleware(['auth','role:admin'])->group(function () {

    
     Route::resource('users', UserController::class, [
        'names' => [
            'index' => 'users.index',
            'show' => 'users.show',
            'edit' => 'users.edit',
            'update' => 'users.update',
            'destroy' => 'users.destroy',
        ]
    ]);

    Route::resource('participants', ParticipantController::class, [
        'names' => [
            'index' => 'participants.index',
            'show' => 'participants.show',
            'create' => 'participants.create',
            'store' => 'participants.store',
            'edit' => 'participants.edit',
            'update' => 'participants.update',
            'destroy' => 'participants.destroy',
        ]
    ]);
     Route::get('participants/{participant}/regenerate-hash', [ParticipantController::class, 'regenerateHashId'])
        ->name('participants.regenerate-hash');
    
    Route::post('participants/{participant}/toggle-status', [ParticipantController::class, 'toggleStatus'])
        ->name('participants.toggle-status');
    
    Route::get('participants/export/csv', [ParticipantController::class, 'export'])
        ->name('participants.export');
    
    Route::get('participants/search', [ParticipantController::class, 'search'])
        ->name('participants.search');
});