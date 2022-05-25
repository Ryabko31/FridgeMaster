<?php

namespace App\Http\Controllers;

use App\Services\BookingFactory;
use OpenApi\Annotations as OA;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *   version="1.0.0",
 *   title="FridgeMaster_api",
 *   @OA\License(name="MIT"),
 *   @OA\Attachable()
 * ),
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Login with email and password to get the authentication token",
 *     name="Token based Based",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="apiAuth",
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $booking;


    public function __construct()
    {
        $this->booking = new BookingFactory();


    }
    public function return_failed(int $code ,string $message){
        return response()->json([
            'status_code' => $code,
            'message' => $message,
        ]);
    }


    public function return_success($data){
        return response()->json([
            'status_code' => 200,
            'data' => $data,
        ]);
    }
}
