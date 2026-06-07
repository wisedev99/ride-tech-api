<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CarController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\ReviewController;


Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1');
Route::post('/login',    [AuthController::class, 'login'])->middleware('throttle:6,1');


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me',      [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // -- Trips visible to both passenger and driver --
    Route::get('/trips',          [TripController::class, 'index']);
    Route::get('/trips/{trip}',   [TripController::class, 'show'])->whereNumber('trip');

    // -- A driver's reviews (any authenticated user) --
    Route::get('/reviews/{driver}', [ReviewController::class, 'index']);

    // -- Driver  --
    Route::middleware('role:driver')->group(function () {
        Route::get('/cars',           [CarController::class, 'index']);
        Route::post('/cars',          [CarController::class, 'store']);
        Route::delete('/cars/{car}',  [CarController::class, 'destroy']);

        Route::get('/trips/available',        [TripController::class, 'available']);
        Route::post('/trips/{trip}/accept',   [TripController::class, 'accept']);
        Route::post('/trips/{trip}/reject',   [TripController::class, 'reject']);
        Route::post('/trips/{trip}/complete', [TripController::class, 'complete']);
    });

    // -- Passenger  --
    Route::middleware('role:passenger')->group(function () {
        Route::post('/trips',           [TripController::class, 'store']);
        Route::put('/trips/{trip}',     [TripController::class, 'update'])->whereNumber('trip');
        Route::delete('/trips/{trip}',  [TripController::class, 'cancel'])->whereNumber('trip');

        Route::post('/reviews/{driver}', [ReviewController::class, 'store']);
    });
});
