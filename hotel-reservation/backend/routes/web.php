<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Room;
use Inertia\Inertia;

Route::get('/', function (Request $request) {
    // On initialise la requête en gardant tes relations (eager loading)
    $query = Room::with('roomType', 'roomType.tariffs');

    // Filtre 1 : Le prix maximum
    if ($request->filled('maxPrice')) {
        $query->where('price', '<=', $request->maxPrice);
    }

    // Filtre 2 : Le type de chambre
    if ($request->filled('type')) {
        if ($request->type === 'standard') {
            $query->where('name', 'like', '%Standard%');
        } elseif ($request->type === 'suite') {
            $query->where('name', 'like', '%Suite%');
        } elseif ($request->type === 'loft') {
            $query->where('name', 'like', '%Loft%');
        }
    }

    // On récupère les 6 résultats correspondants
    $rooms = $query->take(6)->get();
    
    return Inertia::render('Home', [
        'rooms' => $rooms,
        'initialFilters' => $request->only(['arrival', 'departure', 'type', 'maxPrice'])
    ]);
});

Route::get('/chambres/{id}', function ($id) {
    // J'ai aussi nettoyé le \App\Models ici puisque "Room" est importé en haut
    $room = Room::findOrFail($id);
    
    return Inertia::render('RoomDetail', [
        'room' => $room
    ]);
})->name('rooms.show');