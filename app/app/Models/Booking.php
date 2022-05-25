<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;



    protected $fillable = [
        "lcoation_id",
        "user_id",
        "lcoation_id",
        "volume",
        "temperature",
        "price",
        "access_code",
        "date_at",
        "date_to"

    ];

    public function blocks()
    {
        return $this->belongsToMany(Block::class);
    }
}
