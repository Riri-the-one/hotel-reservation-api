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

    // Filtre 3 : Disponibilité par dates (On exclut les chambres occupées)
    if ($request->filled('arrival') && $request->filled('departure')) {
        $arrival = $request->arrival;
        $departure = $request->departure;

        // Attention : Vérifie que tes colonnes s'appellent bien 'check_in' et 'check_out' dans ta migration reservations
        $query->whereDoesntHave('reservations', function ($q) use ($arrival, $departure) {
            $q->where(function ($subQuery) use ($arrival, $departure) {
                // Cas 1 : La réservation existante commence pendant les dates demandées
                $subQuery->whereBetween('check_in', [$arrival, $departure])
                // Cas 2 : La réservation existante se termine pendant les dates demandées
                         ->orWhereBetween('check_out', [$arrival, $departure])
                // Cas 3 : La réservation existante englobe totalement les dates demandées
                         ->orWhere(function ($sq) use ($arrival, $departure) {
                             $sq->where('check_in', '<=', $arrival)
                                ->where('check_out', '>=', $departure);
                         });
            });
        });
    }

    // On récupère les 6 résultats correspondants
    $rooms = $query->take(6)->get();
    
    return Inertia::render('Home', [
        'rooms' => $rooms,
        'initialFilters' => $request->only(['arrival', 'departure', 'type', 'maxPrice'])
    ]);
});

Route::get('/chambres/{id}', function ($id) {
    // On ajoute le "with" ici aussi pour avoir accès au type et aux tarifs sur la page détail
    $room = Room::with('roomType', 'roomType.tariffs')->findOrFail($id);
    
    return Inertia::render('RoomDetail', [
        'room' => $room
    ]);
})->name('rooms.show');