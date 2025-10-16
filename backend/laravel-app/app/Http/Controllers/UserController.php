<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserUpdateRequest;
use App\Services\UserService;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function show()
    {
        $id = auth()->guard('api')->user()->id;

        $user = $this->userService->getUser($id);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function update(UserUpdateRequest $request)
    {
        $id = auth()->guard('api')->user()->id;
        $data = $request->validated();

        $user = $this->userService->updateUser($id, $data);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }
}
