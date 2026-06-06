<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CarController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\ReviewController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me',      [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // -- Driver  --
    Route::middleware('role:driver')->group(function () {
        Route::get('/cars',           [CarController::class, 'index']);
        Route::post('/cars',          [CarController::class, 'store']);
        Route::delete('/cars/{car}',  [CarController::class, 'destroy']);

        Route::get('/trips/available',        [TripController::class, 'available']);
        Route::post('/trips/{trip}/accept',   [TripController::class, 'accept']);
        Route::post('/trips/{trip}/complete', [TripController::class, 'complete']);
    });

    // -- Passenger  --
    Route::middleware('role:passenger')->group(function () {
        Route::post('/trips',               [TripController::class, 'store']);
        Route::post('/trips/{trip}/cancel', [TripController::class, 'cancel']);
        Route::post('/trips/{trip}/reviews',[ReviewController::class, 'store']);
    });
});
