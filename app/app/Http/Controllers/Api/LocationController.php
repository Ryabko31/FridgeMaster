<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/locations",
     *     tags={"locations"},
     *     description="data of locations",
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(response="default", description="list of locations with free room")
     * )
     */
    public function index()
    {
        return  $this->return_success(Location::with('rooms')->withSum('rooms', 'free_blocks')->get());
    }
}
