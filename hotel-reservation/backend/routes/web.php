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
// Ajoute ceci tout à la fin de routes/web.php, après ta route /chambres/{id}

Route::post('/reservations', function (Request $request) {
    // 1. On valide les données reçues
    $request->validate([
        'room_id' => 'required|exists:rooms,id',
        'check_in' => 'required|date|after_or_equal:today',
        'check_out' => 'required|date|after:check_in',
        'guest_name' => 'required|string|max:255',
    ]);

    // 2. On vérifie la disponibilité (sécurité backend)
    $isOccupied = \App\Models\Reservation::where('room_id', $request->room_id)
        ->where(function ($query) use ($request) {
            $query->whereBetween('check_in', [$request->check_in, $request->check_out])
                  ->orWhereBetween('check_out', [$request->check_in, $request->check_out])
                  ->orWhere(function ($q) use ($request) {
                      $q->where('check_in', '<=', $request->check_in)
                        ->where('check_out', '>=', $request->check_out);
                  });
        })->exists();

    if ($isOccupied) {
        return back()->withErrors(['check_in' => 'Désolé, cette chambre vient d\'être réservée pour ces dates.']);
    }

    // 3. On calcule le prix total
    $room = \App\Models\Room::find($request->room_id);
    $checkIn = \Carbon\Carbon::parse($request->check_in);
    $checkOut = \Carbon\Carbon::parse($request->check_out);
    $nights = $checkIn->diffInDays($checkOut);

    // 4. On crée la réservation en base
    \App\Models\Reservation::create([
        'room_id' => $room->id,
        'check_in' => $request->check_in,
        'check_out' => $request->check_out,
        'guest_name' => $request->guest_name,
        'total_price' => $room->price * $nights,
    ]);

    // 5. On redirige vers l'accueil
    return redirect('/')->with('success', 'Votre réservation a bien été confirmée !');
})->name('reservations.store');