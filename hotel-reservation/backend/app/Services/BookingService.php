<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function create(User $user, array $data): Reservation
    {
        return DB::transaction(function () use ($user, $data) {
            // First database operation is a write: serializes SQLite writers, too.
            Room::whereKey($data['room_id'])->update(['updated_at' => now()]);
            $room = Room::with('roomType.tariffs')->lockForUpdate()->findOrFail($data['room_id']);
            $existing = Reservation::where('request_key', $data['request_key'])->first();
            if ($existing) {
                $same = $existing->user_id === $user->id;
                foreach (['room_id', 'guest_name', 'check_in', 'check_out', 'guests'] as $field) {
                    $same = $same && (string) $existing->$field === (string) $data[$field];
                }
                abort_unless($same, 409, 'Request key already used.');

                return $existing;
            }
            if ($room->status !== 'disponible' || $data['guests'] > $room->roomType->capacity) {
                throw ValidationException::withMessages(['guests' => 'Chambre indisponible ou capacité dépassée.']);
            }
            if ($room->reservations()->blocking($data['check_in'], $data['check_out'])->exists()) {
                throw ValidationException::withMessages(['check_in' => 'Chambre indisponible pour ces dates.']);
            }
            $total = 0;
            for ($night = CarbonImmutable::parse($data['check_in']); $night->lt(CarbonImmutable::parse($data['check_out'])); $night = $night->addDay()) {
                $date = $night->toDateString();
                $tariff = $room->roomType->tariffs
                    ->filter(fn ($t) => (! $t->start_date || $t->start_date <= $date) && (! $t->end_date || $t->end_date >= $date))
                    ->sortByDesc('id')->first();
                $price = (string) ($tariff?->price ?? $room->roomType->base_price);
                $cents = (int) bcmul($price, '100', 0);
                if ($cents <= 0) {
                    throw ValidationException::withMessages(['room_id' => 'Aucun tarif valide pour ce séjour.']);
                }
                $total += $cents;
            }
            if ($total > 99999999) {
                throw ValidationException::withMessages(['check_out' => 'Séjour trop long.']);
            }

            return Reservation::create($data + [
                'user_id' => $user->id,
                'room_type_id' => $room->room_type_id,
                'total_price' => bcdiv((string) $total, '100', 2),
                'status' => 'confirmed',
            ]);
        }, 5);
    }

    public function transition(Reservation $reservation, string $from, string $to): Reservation
    {
        return DB::transaction(function () use ($reservation, $from, $to) {
            $query = Reservation::whereKey($reservation->id)->where('status', $from);
            if ($to === 'cancelled') {
                $query->where('check_in', '>', today()->toDateString());
            }
            if ($to === 'checked_in') {
                $query->where('check_in', '<=', today()->toDateString())->where('check_out', '>', today()->toDateString());
            }
            abort_unless($query->update(['status' => $to]) === 1, 409, 'Transition non autorisée.');

            return $reservation->fresh();
        }, 5);
    }
}
