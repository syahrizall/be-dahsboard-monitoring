<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;

Route::post('/webhook/radius-login', [WebhookController::class, 'handle']);
Route::get('test', function () {
    return response()->json(['message' => 'Hello World']);
});
