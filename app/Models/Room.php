<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['room_number', 'floor', 'is_booked', 'travel_time'];

    public function scopeAvailable($query)
    {
        return $query->where('is_booked', false);
    }
}
