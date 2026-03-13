<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TravelOrderController;
use Illuminate\Support\Facades\Route;

// Rotas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rotas protegidas por JWT
Route::middleware('auth:api')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Pedidos de viagem
    Route::get('/travel-orders', [TravelOrderController::class, 'index']);
    Route::post('/travel-orders', [TravelOrderController::class, 'store']);
    Route::get('/travel-orders/{travelOrder}', [TravelOrderController::class, 'show']);
    Route::patch('/travel-orders/{travelOrder}/status', [TravelOrderController::class, 'updateStatus']);
});