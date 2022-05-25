<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CalculateHelpers;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    /**
     * @OA\Post(
     * path="/api/login",
     * summary="Sign in",
     * description="Login by email, password",
     * operationId="authLogin",
     * tags={"auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="13@test.com"),
     *       @OA\Property(property="password", type="string", format="password", example="qazwer123"),
     *    ),
     * ),
     * @OA\Response(
     *    response=422,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
     *        )
     *     )
     * )
     *   @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="user", type="object", ref="App\Models\User"),
     *     )
     *  ),
     */
    public function login(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                if (!Hash::check($request->password, $user->password, [])) {
                    $this->return_failed(401, 'incorrect password');
                }

                $token = $user->createToken('BearerToken');
                CalculateHelpers::updateCountOfFreeBlockInAllLocation();
                return response()->json([
                    'user' => $user,
                    'token' => $token->accessToken
                ]);
            } else {
                $this->return_failed(401, 'incorrect email');
            }
        } catch (Exception $error) {
            $this->return_failed(500, 'Oops something went wrong');
        }
    }



    function register(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->only('email', 'password');

        $valid = validator(
            $data,
            [
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6'
            ]
        );

        if ($valid->fails()) {
            return response()->json($valid->errors()->all(), 400);
        }

        $user = User::create([
            'name' => "test",
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);

        $token = $user->createToken('BearerToken');

        return response()->json([
            'user' => $user,
            'token' => $token->accessToken
        ]);
    }
}
