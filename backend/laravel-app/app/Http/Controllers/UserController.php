<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    /**
     * Show the authenticated user.
     *
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        $id = auth()->guard('api')->user()->id;

        $user = $this->userService->getUser($id);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Update the authenticate user.
     *
     * @param UserUpdateRequest $request
     * @return JsonResponse
     */
    public function update(UserUpdateRequest $request): JsonResponse
    {
        $id = auth()->guard('api')->user()->id;
        $data = $request->validated();

        $user = $this->userService->updateUser($id, $data);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user)
        ]);
    }
}
