<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Tariff;
use App\Models\RoomType;
use App\Models\Room;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

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

    public function userReservations()
    {
        $reservations = Auth::user()->reservations()
            ->with('room', 'room.roomType')
            ->orderBy('check_in', 'desc')
            ->get();

        return Inertia::render('Profile/Reservations', [
            'reservations' => $reservations
        ]);
    }

    public function confirmation(Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            return redirect()->route('dashboard');
        }

        $reservation->load('room', 'room.roomType');

        return Inertia::render('ReservationConfirmation', [
            'reservation' => $reservation
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'guest_name' => 'required|string|max:255',
            'check_in' => 'required|date|after:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        $room = Room::findOrFail($validated['room_id']);
        $roomTypeId = $room->room_type_id;

        $conflict = Reservation::where('room_type_id', $roomTypeId)
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
            return back()->withErrors(['message' => 'Chambre non disponible pour ces dates']);
        }

        $tariff = Tariff::where('room_type_id', $roomTypeId)->first();

        $pricePerNight = $tariff ? $tariff->price : 0;

        $days = Carbon::parse($validated['check_in'])->diffInDays(Carbon::parse($validated['check_out']));
        $totalPrice = $pricePerNight * $days;

        $reservation = Reservation::create([
            'user_id' => Auth::id(),
            'room_id' => $validated['room_id'],
            'room_type_id' => $roomTypeId,
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'total_price' => $totalPrice,
            'status' => 'confirmed',
        ]);

        return redirect()->route('reservations.confirmation', $reservation->id);
    }

    public function cancel(Reservation $reservation)
    {
        if ($reservation->user_id !== Auth::id()) {
            return back()->withErrors(['message' => 'Non autorisé']);
        }

        $reservation->update(['status' => 'cancelled']);

        return back()->with('success', 'Réservation annulée avec succès');
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