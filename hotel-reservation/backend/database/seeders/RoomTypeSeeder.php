<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('room_types')->insert([
            [
                'name' => 'Standard',
                'description' => 'Une chambre confortable avec vue sur la cour intérieure, idéale pour les courts séjours.',
                'capacity' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Suite Premium',
                'description' => 'Spacieuse et lumineuse, cette suite offre un lit king-size et un balcon privé.',
                'capacity' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Loft Familial',
                'description' => 'Un grand espace moderne pensé pour accueillir toute la famille avec un coin salon séparé.',
                'capacity' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
