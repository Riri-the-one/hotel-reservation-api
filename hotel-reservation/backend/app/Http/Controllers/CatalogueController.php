<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CatalogueController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'arrival' => 'nullable|required_with:departure|date_format:Y-m-d|after_or_equal:today',
            'departure' => 'nullable|required_with:arrival|date_format:Y-m-d|after:arrival',
            'type' => 'nullable|integer|exists:room_types,id',
            'maxPrice' => 'nullable|numeric|min:0',
        ]);
        $rooms = Room::with('roomType.tariffs', 'images')->where('status', 'disponible')
            ->when($data['type'] ?? null, fn ($q, $type) => $q->where('room_type_id', $type))
            ->when($data['maxPrice'] ?? null, fn ($q, $price) => $q->whereHas('roomType', fn ($t) => $t->where('base_price', '<=', $price)))
            ->when($data['arrival'] ?? null, fn ($q, $arrival) => $q->whereDoesntHave('reservations', fn ($r) => $r->blocking($arrival, $data['departure'])))
            ->orderBy('room_number')->get();

        return Inertia::render('Home', ['rooms' => $rooms, 'roomTypes' => RoomType::all(), 'filters' => $data]);
    }
}
