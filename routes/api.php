<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\AuthController;

// Authentication routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

// Webhook routes
Route::post('/webhook', [WebhookController::class, 'receive'])
    ->middleware('rate.limit:30'); // 30 requests per minute for webhook

// PrivacyIDEA specific webhook route
Route::post('/webhook/privacyidea', [WebhookController::class, 'privacyidea'])
    ->middleware('rate.limit:30'); // 30 requests per minute for PrivacyIDEA webhook

Route::middleware('auth:sanctum')->prefix('stats')->group(function () {
    Route::get('/active-users', [StatsController::class, 'activeUsers'])
        ->middleware('rate.limit:60');
    Route::get('/unique-users', [StatsController::class, 'uniqueUsers'])
        ->middleware('rate.limit:60');
    Route::get('/list-unique-users', [StatsController::class, 'listUniqueUsers'])
        ->middleware('rate.limit:60');
    Route::get('/last-login', [StatsController::class, 'lastLogin'])
        ->middleware('rate.limit:60');
    Route::get('/success-logins', [StatsController::class, 'successCount'])
        ->middleware('rate.limit:60');
    Route::get('/failed-logins', [StatsController::class, 'failedCount'])
        ->middleware('rate.limit:60');
    Route::get('/logins-by-date', [StatsController::class, 'loginsByDate'])
        ->middleware('rate.limit:60');
});
