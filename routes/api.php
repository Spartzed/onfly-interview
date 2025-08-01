<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TravelOrderController;

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

// Rotas públicas
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

// Rotas protegidas
Route::middleware('auth:api')->group(function () {
    // Auth
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);

    // Travel Orders
    Route::prefix('travel-orders')->group(function () {
        Route::get('/', [TravelOrderController::class, 'index']);
        Route::post('/', [TravelOrderController::class, 'store']);
        Route::get('/{id}', [TravelOrderController::class, 'show']);
        Route::patch('/{id}/status', [TravelOrderController::class, 'updateStatus']);
        Route::delete('/{id}', [TravelOrderController::class, 'cancel']);
        Route::get('/by-order-id/{orderId}', [TravelOrderController::class, 'getByOrderId']);
    });
}); 