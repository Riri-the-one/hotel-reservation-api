<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Models\Room;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Home page with rooms listing
Route::get('/', function () {
    try {
        $rooms = Room::with('roomType', 'roomType.tariffs')->take(6)->get();
        
        return Inertia::render('Home', [
            'rooms' => $rooms,
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()], 500);
    }
});

// Debug route to test Inertia response
Route::get('/debug-inertia', function () {
    return Inertia::render('Home', [])->toResponse(request())->getContent();
});

// Room detail page
Route::get('/chambres/{id}', function ($id) {
    $room = Room::with('roomType', 'roomType.tariffs')->findOrFail($id);
    return Inertia::render('RoomDetail', [
        'room' => $room
    ]);
})->name('rooms.show');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // User reservations
    Route::get('/profile/reservations', [ReservationController::class, 'userReservations'])->name('profile.reservations');
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel']);
});

// Reservation confirmation page
Route::get('/reservations/{reservation}/confirmation', [ReservationController::class, 'confirmation'])
    ->middleware('auth')
    ->name('reservations.confirmation');

require __DIR__.'/auth.php';
