<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return ['total_price' => 'decimal:2', 'guests' => 'integer'];
    }

    protected $fillable = [
        'room_id',
        'room_type_id',
        'user_id',
        'guest_name',
        'check_in',
        'check_out',
        'total_price',
        'status', 'guests', 'request_key',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeBlocking($query, string $arrival, string $departure)
    {
        return $query->whereIn('status', ['confirmed', 'checked_in'])
            ->where('check_in', '<', $departure)->where('check_out', '>', $arrival);
    }
}
