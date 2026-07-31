<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomImage extends Model
{
    use HasFactory;

    // Autorise l'enregistrement de ces deux colonnes
    protected $fillable = ['room_id', 'path'];

    // Une image appartient à une seule chambre
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}