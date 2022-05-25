<?php

namespace App\Helpers;

use App\Models\Block;
use App\Models\Location;
use Carbon\Carbon;

class CalculateHelpers
{

    public static function countOfBlocks(int $order_volume)
    {
        return round($order_volume / 2,  PHP_ROUND_HALF_UP);
    }


    public static function countOfFreeBlockWithTemperature(object $location, int $temperature)
    {
        $count = 0;

        foreach ($location->rooms->whereBetween('temperature', self::temperaureRange($temperature)) as $room) {
            $count += $room->blocks->where('is_reserve', false)->count();
        }
        return $count;
    }


    public static function countOfFreeBlock(object $location)
    {
        $count = 0;
        foreach ($location->rooms as $room) {
            $count += $room->blocks->where('is_reserve', false)->count();
        }
        return $count;
    }


    public static function updateCountOfFreeBlockInRooms(object $location)
    {

        foreach ($location->rooms as $room) {
            $room->free_blocks = $room->blocks->where('is_reserve', false)->count();
            $room->save();
        }
        return true;
    }


    public static function updateCountOfFreeBlockInAllLocation()
    {

        $locations = Location::all();
        foreach ($locations as $location) {
            foreach ($location->rooms as $room) {
                $room->free_blocks = $room->blocks->where('is_reserve', false)->count();
                $room->save();
            }
        }
        return true;
    }



    public static function idOfFreeBlock(object $location, int $temperature, int $order_volume)
    {

        $blok_id = [];
        foreach ($location->rooms->whereBetween('temperature', self::temperaureRange($temperature)) as $room) {
            foreach ($room->blocks as $block) {
                if ($block->is_reserve == false) {
                    if (count($blok_id) < self::countOfBlocks($order_volume)) {
                        $blok_id[] = $block->id;
                    }
                }
            }
        }
        return $blok_id;
    }

    public static function countOfDay($date_at, $date_to)
    {
        $at = Carbon::createFromFormat('Y-m-d', $date_at);
        $to = Carbon::createFromFormat('Y-m-d', $date_to);
        $diff_in_day = $at->diffInDays($to);
        return $diff_in_day;
    }


    public static function updateBlocks(array $data, $date_at = null, $date_to = null, bool $is_reseve)
    {

        $blocks = Block::find($data);
        foreach ($blocks as $block) {
            $block->is_reserve = $is_reseve;
            $block->reserve_at = $date_at;
            $block->reserve_to = $date_to;
            $block->save();
        }
        return true;
    }


    public static function temperaureRange(int $temperature)
    {
        $temp_at = 0;
        $temp_to = $temperature + 2;
        if ($temperature - 2 <= 0) {
            $temp_at = 0;
        } else {
            $temp_at = $temperature - 2;
        }
        $data = [$temp_at, $temp_to];

        return $data;
    }
}
