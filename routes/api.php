<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    
    // ==================== AUTHENTICATION ====================
    Route::controller(AuthController::class)->group(function () {
        Route::post('/login', 'login');
        Route::post('/register', 'register');
        Route::post('/logout', 'logout')->middleware('auth:sanctum');
        Route::get('/profile', 'profile')->middleware('auth:sanctum');
    });
    
    // ==================== EVENTS ====================
    Route::controller(EventApiController::class)->group(function () {
        // Public
        Route::get('/events', 'index');
        Route::get('/events/{id}', 'show');
        Route::get('/events/slug/{slug}', 'showBySlug');
        
        // Protected
        Route::post('/events/{id}/book', 'book')->middleware('auth:sanctum');
        Route::get('/my-events', 'myEvents')->middleware('auth:sanctum');
    });
    
    // ==================== CATEGORIES ====================
    Route::controller(CategoryApiController::class)->group(function () {
        Route::get('/categories', 'index');
        Route::get('/categories/{id}', 'show');
        Route::get('/categories/{id}/events', 'events');
    });
    
    // ==================== ORDERS & TICKETS ====================
    Route::controller(OrderApiController::class)->middleware('auth:sanctum')->group(function () {
        Route::post('/orders', 'store');
        Route::get('/my-orders', 'myOrders');
        Route::get('/orders/{id}', 'show');
        Route::post('/orders/{id}/upload-payment', 'uploadPaymentProof');
        Route::post('/orders/{id}/cancel', 'cancel');
        Route::get('/my-tickets', 'myTickets');
        Route::get('/tickets/{ticket_code}', 'getTicketDetail');
    });
    
    // ==================== PUBLIC TICKET CHECK ====================
    Route::get('/check-ticket/{ticket_code}', [OrderApiController::class, 'checkTicket']);
});