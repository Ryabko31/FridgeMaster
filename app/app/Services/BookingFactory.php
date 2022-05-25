<?php

namespace App\Services;

use App\Models\Booking;
use App\Services\BookingServices\BookingServices;
use App\Services\Editors\MessageEditor;


class BookingFactory
{

    static public function bookingSercives()
    {
        return new BookingServices();
    }


}
