<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;


    public function rooms()
    {
        return $this->hasMany(Room::class, 'location_id', 'id');
    }

    public function freeSpace()
    {


        $freeSpase=$this->rooms()->sum('free_blocks');
        // dd( $freeSpase);
        return  $freeSpase;
    }

    public function maxSpace()
    {
            $maxeSpace=0;
            foreach($this->rooms as $room){
                $maxeSpace +=$room->allBlock();
            }
            return $maxeSpace;
    }
}
