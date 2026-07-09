<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\Reservation;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        $loft = Room::where('name', 'like', '%Loft%')->first();

        if ($loft) {
            Reservation::create([
                'room_id' => $loft->id,
                'check_in' => '2026-07-24',
                'check_out' => '2026-08-02',
                'guest_name' => 'Réservation Groupe (3 personnes)',
                'total_price' => $loft->price * 9,
            ]);
        }
    }
}