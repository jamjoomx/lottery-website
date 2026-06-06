<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DrawController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login',    [AuthController::class, 'login']);
Route::get('/draws/latest',   [DrawController::class, 'latest']);

// Authenticated user routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout',   [AuthController::class, 'logout']);
    Route::get('/tickets',        [TicketController::class, 'index']);
    Route::post('/tickets/buy',   [TicketController::class, 'buy']);
    Route::get('/tickets/{id}/result', [TicketController::class, 'checkResult']);
});

// Admin routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    Route::get('/draws',         [DrawController::class, 'index']);
    Route::post('/draws',        [DrawController::class, 'create']);
    Route::post('/draws/{id}/execute', [DrawController::class, 'execute']);
});
