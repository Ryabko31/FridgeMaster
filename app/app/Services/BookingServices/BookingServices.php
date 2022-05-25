<?php

namespace App\Services\BookingServices;

use Exception;

use App\Models\Booking;
use App\Helpers\CalculateHelpers;
use App\Models\Block;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\Foreach_;

class BookingServices
{

    public function calculateBooking(object $location, int $temperature, int $volume,  $date_at, $date_to)
    {
        $blocks = CalculateHelpers::idOfFreeBlock($location, $temperature, $volume);
        if (!$blocks) {
            return null;
        }
        $data = [
            'block_id' => $blocks,
            'temperature' => $temperature,
            'volume' => $volume,
            'price' => (CalculateHelpers::countOfBlocks($volume) * 10) * CalculateHelpers::countOfDay($date_at, $date_to),
            'date_at' => $date_at,
            'date_to' => $date_to
        ];

        return $data;
    }

    public function createBooking(object $location, array $data,  int $user_id)
    {
        DB::beginTransaction();
        try {
            $booking = new Booking();
            $booking->user_id = $user_id;
            $booking->location_id = $location->id;
            $booking->volume = $data['volume'];
            $booking->temperature = $data['temperature'];
            $booking->price = $data['price'];
            $booking->access_code = Str::random(12);;
            $booking->date_at = $data['date_at'];
            $booking->date_to = $data['date_to'];
            $booking->save();

            CalculateHelpers::updateBlocks($data['block_id'],$data['date_at'], $data['date_to'], true );
            CalculateHelpers::updateCountOfFreeBlockInRooms($location);
            $booking->blocks()->attach($data['block_id']);

            DB::commit();

            return $booking->id;
        } catch (Exception $e) {
            Log::debug("$e");
            DB::rollBack();
            return false;
        }
    }

    public function updateBooking(object $model, array $data)
    {
        //The future method)))
    }
}
