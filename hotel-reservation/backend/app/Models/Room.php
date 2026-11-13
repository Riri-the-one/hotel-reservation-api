<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['room_type_id', 'room_number', 'status', 'image'];

    // Une chambre appartient à un seul type de chambre
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    // Une chambre peut avoir plusieurs réservations
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // NOUVEAU : Une chambre peut avoir plusieurs images dans la galerie
    public function images()
    {
        return $this->hasMany(RoomImage::class);
    }
}
