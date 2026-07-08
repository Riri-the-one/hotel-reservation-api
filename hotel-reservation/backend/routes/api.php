<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\TariffController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

// --- ROUTES PUBLIQUES (Accessibles sans être connecté) ---
Route::post('/login', [AuthController::class, 'login']);
Route::get('/availabilities', [ReservationController::class, 'checkAvailability']);


// --- ROUTES PRIVÉES (Nécessitent un Token d'authentification) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // CRUD de base
    Route::apiResource('room-types', RoomTypeController::class);
    Route::apiResource('tariffs', TariffController::class);
    
    // Disponibilités des chambres (DOIT être avant apiResource('rooms'))
    Route::post('/rooms/available', [ReservationController::class, 'availableRooms']);
    Route::apiResource('rooms', RoomController::class);
    
    // Réservations
    Route::get('/reservations', [ReservationController::class, 'index']); // Historique
    Route::post('/reservations', [ReservationController::class, 'store']); // Créer une réservation
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel']); // Annuler
    Route::post('/reservations/{reservation}/check-in', [ReservationController::class, 'checkIn']); // Check-in
    Route::post('/reservations/{reservation}/check-out', [ReservationController::class, 'checkOut']); // Check-out
});