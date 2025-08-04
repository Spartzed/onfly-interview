<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TravelOrderController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\DashboardStatsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

// Rotas protegidas
Route::middleware('auth:api')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);

    Route::prefix('travel-orders')->group(function () {
        Route::get('/', [TravelOrderController::class, 'index']);
        Route::post('/', [TravelOrderController::class, 'store']);
        Route::put('/{id}', [TravelOrderController::class, 'update']);
        Route::get('/{id}', [TravelOrderController::class, 'show']);
        Route::patch('/{id}/status', [TravelOrderController::class, 'updateStatus']);
        Route::delete('/{id}', [TravelOrderController::class, 'cancel']);
        Route::get('/by-order-id/{orderId}', [TravelOrderController::class, 'getByOrderId']);
    });

    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread', [NotificationController::class, 'unread']);
        Route::get('/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    });

    Route::prefix('stats')->middleware('admin')->group(function () {
        Route::get('/', [DashboardStatsController::class, 'index']);
    });
}); 