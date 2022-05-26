<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Helpers\CalculateHelpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/locations/{location_id}/bookingCalulte",
     * description="Calculate booking",
     * operationId="bookingCalulte",
     * tags={"booking"},
     *      security={{ "apiAuth": {} }},
     *      @OA\Parameter(
     *         name="location_id",
     *         in="path",
     *         description="Buscar por estado",
     *         required=true,
     *      ),
     * @OA\RequestBody(
     *    required=true,
     *    description="data to calculate",
     *    @OA\JsonContent(
     *       required={"volume","temperature", "date_at", "date_to"},
     *       @OA\Property(property="volume", type="int", example="20"),
     *       @OA\Property(property="temperature", type="int", example="2"),
     *       @OA\Property(property="date_at", type="date", example="2022-05-01"),
     *       @OA\Property(property="date_to", type="date", example="2022-05-01"),
     *    ),
     * ),
     * @OA\Response(
     *    response=500,
     *    description="error",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Opps")
     *        )
     *     )
     * ),
     *
     */
    public function calculateOrder(Location $location, Request $request)
    {
        if (CalculateHelpers::countOfDay($request->date_at, $request->date_to) > 24) {
            return $this->return_failed(402, "Максимальний період зберігання 24 дні");
        }

        if (CalculateHelpers::countOfFreeBlockWithTemperature($location, $request->temperature) < CalculateHelpers::countOfBlocks($request->volume)) {
            return $this->return_failed(402, "У вашому замовленні перевищенно дуступні потужності зберігання, виш запит $request->volume доступно: " . CalculateHelpers::countOfFreeBlockWithTemperature($location, $request->temperature) * 2);
        }


        return $this->return_success($this->booking::bookingSercives()->calculateBooking($location,  $request->temperature, $request->volume, $request->date_at, $request->date_to));
    }

    /**
     * @OA\Post(
     * path="/api/locations/{location_id}/bookingConfirm",
     * description="Confirm booking",
     * operationId="bookingConfirm",
     * tags={"booking"},
     *     security={{ "apiAuth": {} }},
     *      @OA\Parameter(
     *         name="location_id",
     *         in="path",
     *         description="location",
     *         required=true,
     *      ),
     * @OA\RequestBody(
     *    required=true,
     *    description="data of booking",
     *    @OA\JsonContent(
     *       required={"booking"},
     *       @OA\Property(property="booking", type="object", example= {"block_id": {1,2,3,4},"temperature": 2,"volume": 2,"price": 0,"date_at": "2022-05-01","date_to": "2022-05-01"}),
     *    ),
     * ),
     * @OA\Response(
     * response=200,
     * description="Successful operation",
     * @OA\JsonContent(
     *       @OA\Property(property="code", type="int", example="200"),
     *        @OA\Property(property="booking_id", type="int", example="800")
     *        ),
     * ),
     * @OA\Response(
     *  response=401,
     *  description="Unauthenticated",
     *  @OA\JsonContent(
     *      @OA\Property(property="message", type="string", example="Not authorized"),
     *  )
     * ),
     * @OA\Response(
     *  response=403,
     *  description="Forbidden"
     * ),
     * )
     */
    public function booking(Location $location, Request $request)
    {
            //Потрібно змінити логіку формування замовлення в частині визначення, вільних блоків при бронюванні
        return $this->return_success($this->booking::bookingSercives()->createBooking($location,  $request->booking, Auth::user()->id));
    }
}
