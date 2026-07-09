<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    use HasFactory;

    protected $fillable = ['room_type_id', 'price', 'start_date', 'end_date'];

    // Un tarif appartient à un type de chambre
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
}
