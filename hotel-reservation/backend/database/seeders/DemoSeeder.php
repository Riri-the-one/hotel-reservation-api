<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production') || RoomType::exists()) {
            throw new \RuntimeException('Demo seeding requires an empty non-production database.');
        }
        foreach ([['Standard', 2, '75.00', 'Un espace confortable et lumineux pour une parenthèse à deux.'], ['Suite Premium', 2, '150.00', 'Une suite spacieuse avec un coin salon pour prolonger les moments de calme.'], ['Loft Familial', 4, '210.00', 'Un espace généreux pour partager un séjour en famille.']] as $i => [$name, $capacity, $price, $description]) {
            $type = RoomType::create(['name' => $name, 'capacity' => $capacity, 'base_price' => $price, 'description' => $description]);
            foreach ([1, 2] as $number) {
                Room::create(['room_type_id' => $type->id, 'room_number' => (string) (($i + 1) * 100 + $number), 'status' => 'disponible']);
            }
        }
    }
}
