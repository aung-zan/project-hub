<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\User\UserCreateRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Store a user with validated data.
     *
     * @param UserCreateRequest $request
     * @return JsonResponse
     */
    public function register(UserCreateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = $this->userService->createUser($data);

        return response()->json([
            'success' => true,
            'message' => 'User register successfully.',
            'data' => $user
        ]);
    }

    /**
     * Return the access token if a user with validated data pass
     * the authentication.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentails = $request->validated();

        if (!$token = auth()->guard('api')->attempt($credentails)) {
            return response()->json([
                'success' => false,
                'message' => 'Email or password is wrong.',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successfully.',
            'access_token' => $token,
            'token_type' => 'bearer',
        ]);
    }

    /**
     * Logout function.
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        /**
         * 1. Short lived access token + refresh token.
         * 2. Blacklists table on redis server.
         */

        return response()->json([
            'success' => true,
            'message' => 'logout',
        ]);
    }
}
