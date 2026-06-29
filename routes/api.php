<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EventApiController;
use App\Http\Controllers\API\OrderApiController;
use App\Http\Controllers\API\CategoryApiController;
use App\Http\Controllers\API\MerchandiseApiController;
use App\Http\Controllers\API\OrganisationApiController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\ParticipantAttendanceController;

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
        Route::post('/upgrade-to-member', 'upgradeToMember')->middleware('auth:sanctum');
    });
    
    // ==================== EVENTS ====================
    Route::controller(EventApiController::class)->group(function () {
        Route::get('/events', 'index');
        Route::get('/events/{id}', 'show');
        Route::get('/events/slug/{slug}', 'showBySlug');
        Route::get('/events/{id}/participants/count', 'participantCount');
        Route::get('/events/{id}/merchandise', 'eventMerchandise'); // NEW: Get all merchandise for event
        Route::get('/events/{eventId}/merchandise/{merchandiseId}', 'eventMerchandiseDetail'); // NEW: Get specific merchandise detail
        Route::get('/sponsors', 'getSponsors');
        
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/events/{id}/book', 'book');
            Route::get('/my-events', 'myEvents');
            Route::get('/events/{id}/participants', 'participants');
        });
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
        Route::get('/orders/{id}', 'show');
        Route::post('/orders/{id}/upload-payment', 'uploadPaymentProof');
        Route::post('/orders/{id}/cancel', 'cancel');
        Route::get('/my-tickets', 'myTickets');
        Route::get('/tickets/{ticket_code}', 'getTicketDetail');
    });

    // ==================== MERCHANDISE ====================
    Route::prefix('merchandise')->group(function () {
        // Public routes
        Route::get('/', [MerchandiseApiController::class, 'index']);
        Route::get('/categories', [MerchandiseApiController::class, 'categories']);
        Route::get('/{id}', [MerchandiseApiController::class, 'show']);
        
        // Protected routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/order', [MerchandiseApiController::class, 'createOrder']);
            Route::get('/my-orders', [MerchandiseApiController::class, 'myOrders']);
            Route::get('/orders/{id}', [MerchandiseApiController::class, 'orderDetail']);
            Route::post('/orders/{id}/cancel', [MerchandiseApiController::class, 'cancelOrder']);
            
            // ========== NEW: PAYMENT PROOF ROUTES ==========
            Route::post('/orders/{id}/upload-payment', [MerchandiseApiController::class, 'uploadPaymentProof']);
            Route::get('/orders/{id}/payment-status', [MerchandiseApiController::class, 'getPaymentStatus']);
        });
    });

    // ==================== ORGANISATION HIERARCHY ====================
    Route::prefix('organisations')->controller(OrganisationApiController::class)->group(function () {
        // All routes are public (read only)
        Route::get('/', 'index');                                    // List all hierarchies
        Route::get('/tree', 'tree');                                 // Tree structure
        Route::get('/years', 'getYears');                            // Available years
        Route::get('/stats', 'getStats');                            // Statistics
        Route::get('/levels', 'getLevels');                          // Available levels
        Route::get('/search', 'search');                             // Search
        Route::get('/year/{year}', 'getByYear');                     // By year
        Route::get('/level/{level}', 'getByLevel');                  // By level
        Route::get('/{id}', 'show');                                 // Detail
        Route::get('/{id}/holders', 'getHolders');                   // Holders by hierarchy
        Route::get('/holders/{id}', 'getHolder');                    // Holder detail
    });
    
    // ==================== PUBLIC ====================
    Route::get('/check-ticket/{ticket_code}', [OrderApiController::class, 'checkTicket']);
    
    // ==================== PARTICIPANTS PROFILE ====================
    Route::prefix('participants')->middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'profile']);
        Route::put('/me', [ProfileController::class, 'update']);
        Route::post('/me/photo', [ProfileController::class, 'uploadPhoto']);
    });
    
    // ==================== PARTICIPANT ATTENDANCE (QR CODE) ====================
    Route::prefix('participant')->middleware('auth:sanctum')->group(function () {
        // Events
        Route::get('/events', [ParticipantAttendanceController::class, 'myEvents']);
        Route::get('/events/{eventId}', [ParticipantAttendanceController::class, 'eventDetail']);
        
        // QR Code
        Route::get('/events/{eventId}/qrcode', [ParticipantAttendanceController::class, 'getQrCodeImage']);
        Route::get('/events/{eventId}/qrcode/base64', [ParticipantAttendanceController::class, 'getQrCodeBase64']);
        Route::get('/events/{eventId}/attendance-status', [ParticipantAttendanceController::class, 'getAttendanceStatus']);
        
        // History
        Route::get('/attendance-history', [ParticipantAttendanceController::class, 'attendanceHistory']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/attendance/scan/{qrCode?}', [ParticipantAttendanceController::class, 'scan']);
    });
});
