<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('rooms')->insert([
            [
                'room_type_id' => 1, // Standard
                'room_number' => '101',
                'status' => 'disponible',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_type_id' => 1, // Standard
                'room_number' => '102',
                'status' => 'disponible',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_type_id' => 2, // Suite Premium
                'room_number' => '201',
                'status' => 'disponible',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_type_id' => 3, // Loft Familial
                'room_number' => '301',
                'status' => 'disponible',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
