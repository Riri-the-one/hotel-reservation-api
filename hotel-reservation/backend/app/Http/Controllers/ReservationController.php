<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Models\Reservation;
use App\Models\Room;
use App\Services\BookingService;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        return response()->json($request->user()->reservations()->with('room.roomType')->latest()->get());
    }

    public function userReservations(Request $request)
    {
        return Inertia::render('Profile/Reservations', ['reservations' => $request->user()->reservations()->with('room.roomType')->latest()->get()]);
    }

    public function confirmation(Reservation $reservation)
    {
        Gate::authorize('view', $reservation);

        return Inertia::render('ReservationConfirmation', ['reservation' => $reservation->load('room.roomType')]);
    }

    public function store(StoreReservationRequest $request, BookingService $bookings)
    {
        try {
            $reservation = $bookings->create($request->user(), $request->validated());
        } catch (UniqueConstraintViolationException $exception) {
            abort(409, 'Request key already used.');
        }

        return $request->expectsJson() ? response()->json($reservation, 201) : redirect()->route('reservations.confirmation', $reservation);
    }

    public function cancel(Request $request, Reservation $reservation, BookingService $bookings)
    {
        Gate::authorize('cancel', $reservation);
        $reservation = $bookings->transition($reservation, 'confirmed', 'cancelled');

        return $request->expectsJson() ? response()->json($reservation) : back()->with('success', 'Réservation annulée.');
    }

    public function checkIn(Request $request, Reservation $reservation, BookingService $bookings)
    {
        Gate::authorize('manage-hotel');

        $reservation = $bookings->transition($reservation, 'confirmed', 'checked_in');

        return $request->expectsJson() ? response()->json($reservation) : back()->with('success', 'Check-in effectué.');
    }

    public function checkOut(Request $request, Reservation $reservation, BookingService $bookings)
    {
        Gate::authorize('manage-hotel');

        $reservation = $bookings->transition($reservation, 'checked_in', 'checked_out');

        return $request->expectsJson() ? response()->json($reservation) : back()->with('success', 'Check-out effectué.');
    }

    public function checkAvailability(Request $request)
    {
        $data = $request->validate([
            'check_in' => 'required|date_format:Y-m-d|after_or_equal:today',
            'check_out' => 'required|date_format:Y-m-d|after:check_in',
        ]);

        return response()->json(['available_rooms' => Room::with('roomType')->where('status', 'disponible')
            ->whereDoesntHave('reservations', fn ($q) => $q->blocking($data['check_in'], $data['check_out']))->get()]);
    }
}
