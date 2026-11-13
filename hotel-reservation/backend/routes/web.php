<?php

use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [CatalogueController::class, 'index'])->name('home');

// Room detail page
Route::get('/chambres/{id}', function ($id) {
    $room = Room::with('roomType.tariffs', 'images')->findOrFail($id);

    return Inertia::render('RoomDetail', [
        'room' => $room,
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

Route::post('/admin/reservations/{reservation}/check-in', [ReservationController::class, 'checkIn'])->middleware(['auth', 'can:manage-hotel']);
Route::post('/admin/reservations/{reservation}/check-out', [ReservationController::class, 'checkOut'])->middleware(['auth', 'can:manage-hotel']);

Route::get('/admin/reservations', function () {
    return Inertia::render('Admin/Reservations', [
        'reservations' => Reservation::with('room.roomType')->latest()->get(),
    ]);
})->middleware(['auth', 'can:manage-hotel'])->name('admin.reservations');
