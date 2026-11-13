<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\TariffController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // Room Types
    Route::apiResource('room-types', RoomTypeController::class)->middleware('can:manage-hotel');

    // Tariffs
    Route::apiResource('tariffs', TariffController::class)->middleware('can:manage-hotel');

    // Rooms
    Route::apiResource('rooms', RoomController::class)->middleware('can:manage-hotel');

    // Reservations
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel']);
    Route::post('/reservations/{reservation}/check-in', [ReservationController::class, 'checkIn']);
    Route::post('/reservations/{reservation}/check-out', [ReservationController::class, 'checkOut']);
});

// Public route for availability check
Route::get('/reservations/check-availability', [ReservationController::class, 'checkAvailability']);
