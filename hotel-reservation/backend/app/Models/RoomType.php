<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Un type de chambre possède plusieurs chambres
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}