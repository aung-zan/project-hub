<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectUser\ProjectUserCreateRequest;
use App\Services\ProjectUserService;

class ProjectUserController extends Controller
{
    public function __construct(private ProjectUserService $projectUserService)
    {
    }

    public function store(int $projectId, ProjectUserCreateRequest $request)
    {
        $data = $request->validated();
        $data['auth_id'] = auth()->guard('api')->id();

        $members = $this->projectUserService->createProjectUser($projectId, $data);

        return response()->json([
            'success' => true,
            'data' => [
                'members' => $members,
            ],
        ], 200);
    }

    public function destroy(int $projectId, int $memberId)
    {
        $this->projectUserService->removeProjectUser($projectId, $memberId);

        return response()->json([
            'success' => true,
            'message' => 'Successfully remove a member.'
        ], 200);
    }
}
