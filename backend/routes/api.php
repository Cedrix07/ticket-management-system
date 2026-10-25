<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

   Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::apiResource('ticket-categories', CategoryController::class);
        Route::patch('ticket-categories/{ticket_category}/status', [CategoryController::class, 'toggleStatus']);
   });
});
