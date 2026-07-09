<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TariffSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tariffs')->insert([
            [
                'room_type_id' => 1, // Standard
                'price' => 75.00,
                'start_date' => now()->subDays(30),
                'end_date' => now()->addDays(365),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_type_id' => 2, // Suite Premium
                'price' => 150.00,
                'start_date' => now()->subDays(30),
                'end_date' => now()->addDays(365),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'room_type_id' => 3, // Loft Familial
                'price' => 210.00,
                'start_date' => now()->subDays(30),
                'end_date' => now()->addDays(365),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
