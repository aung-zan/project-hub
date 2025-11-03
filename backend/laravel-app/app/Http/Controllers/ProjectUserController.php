<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectUser\ProjectUserCreateRequest;
use App\Models\Project;
use App\Services\ProjectUserService;

class ProjectUserController extends Controller
{
    public function __construct(private ProjectUserService $projectUserService)
    {
    }

    public function store(Project $project, ProjectUserCreateRequest $request)
    {
        $data = $request->validated();
        $data['auth_id'] = auth()->guard('api')->id();

        $members = $this->projectUserService->createProjectUser($project, $data);

        return response()->json([
            'success' => true,
            'data' => [
                'members' => $members,
            ],
        ], 200);
    }

    public function destroy(Project $project, int $memberId)
    {
        $this->projectUserService->removeProjectUser($project, $memberId);

        return response()->json([
            'success' => true,
            'message' => 'Successfully remove a member.'
        ], 200);
    }
}
