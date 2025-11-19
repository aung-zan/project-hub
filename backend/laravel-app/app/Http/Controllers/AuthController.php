<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(private UserService $userService)
    {
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
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Return the access token if authenticated.
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
                'error' => 'UNAUTHORIZED_ACCESS',
                'message' => 'Email or password is wrong.',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successfully.',
            'data' => [
                'user' => new UserResource(auth()->guard('api')->user()),
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl', 3600) . ' seconds',
                'access_token' => $token,
            ],
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
