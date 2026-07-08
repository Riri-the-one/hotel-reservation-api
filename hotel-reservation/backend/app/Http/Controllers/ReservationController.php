<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Tariff;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $reservations = Auth::user()->reservations()
            ->with('roomType')
            ->orderBy('check_in')
            ->get();

        return response()->json($reservations);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'check_in' => 'required|date|after:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        $conflict = Reservation::where('room_type_id', $validated['room_type_id'])
            ->where('status', 'confirmed')
            ->where(function ($query) use ($validated) {
                $query->whereBetween('check_in', [$validated['check_in'], $validated['check_out']])
                      ->orWhereBetween('check_out', [$validated['check_in'], $validated['check_out']])
                      ->orWhere(function ($q) use ($validated) {
                          $q->where('check_in', '<=', $validated['check_in'])
                            ->where('check_out', '>=', $validated['check_out']);
                      });
            })->exists();

        if ($conflict) {
            return response()->json(['message' => 'Chambre non disponible pour ces dates'], 422);
        }

        $tariff = Tariff::where('room_type_id', $validated['room_type_id'])->first();

        $pricePerNight = $tariff ? $tariff->price : 0;

        $days = Carbon::parse($validated['check_in'])->diffInDays(Carbon::parse($validated['check_out']));
        $totalPrice = $pricePerNight * $days;

        $reservation = Reservation::create([
            'user_id' => Auth::id(),
            'room_type_id' => $validated['room_type_id'],
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'total_price' => $totalPrice,
            'status' => 'confirmed',
        ]);

        return response()->json($reservation, 201);
    }

    public function cancel(Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reservation->update(['status' => 'cancelled']);

        return response()->json([
            'message' => 'Réservation annulée avec succès',
            'reservation' => $reservation
        ]);
    }

    public function checkIn(Reservation $reservation)
    {
        if ($reservation->status !== 'confirmed') {
            return response()->json(['message' => 'Impossible de faire le check-in, la réservation n\'est pas confirmée'], 400);
        }

        $reservation->update(['status' => 'checked_in']);

        return response()->json([
            'message' => 'Check-in effectué avec succès',
            'reservation' => $reservation
        ]);
    }

    public function checkOut(Reservation $reservation)
    {
        if ($reservation->status !== 'checked_in') {
            return response()->json(['message' => 'Impossible de faire le check-out, la réservation n\'est pas check-in'], 400);
        }

        $reservation->update(['status' => 'checked_out']);

        return response()->json([
            'message' => 'Check-out effectué avec succès',
            'reservation' => $reservation
        ]);
    }

    public function checkAvailability(Request $request)
    {
        $validated = $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        $checkIn = $validated['check_in'];
        $checkOut = $validated['check_out'];

        // 1. Trouver les IDs des types de chambres DÉJÀ réservées sur ces dates
        $bookedRoomTypeIds = Reservation::whereIn('status', ['confirmed', 'checked_in'])
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out', [$checkIn, $checkOut])
                      ->orWhere(function ($q) use ($checkIn, $checkOut) {
                          $q->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                      });
            })->pluck('room_type_id');

        // 2. Récupérer uniquement les chambres qui ne sont PAS dans la liste bloquée
        $availableRooms = RoomType::whereNotIn('id', $bookedRoomTypeIds)->get();

        return response()->json([
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'available_rooms' => $availableRooms
        ], 200);
    }
}