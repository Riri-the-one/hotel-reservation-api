<?php

use Illuminate\Support\Facades\Route;
use App\Models\Room;
use Inertia\Inertia;

Route::get('/', function () {
    $rooms = Room::with('roomType', 'roomType.tariffs')->take(6)->get();
    
    return Inertia::render('Home', [
        'rooms' => $rooms
    ]);
});

Route::get('/chambres/{id}', function ($id) {
    $room = \App\Models\Room::with('roomType', 'roomType.tariffs')->findOrFail($id);
    return Inertia::Inertia::render('RoomDetail', [
        'room' => $room
    ]);
})->name('rooms.show');
