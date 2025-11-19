<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectUser\ProjectUserCreateRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\ProjectUserService;
use Illuminate\Http\JsonResponse;

class ProjectUserController extends Controller
{
    public function __construct(private ProjectUserService $projectUserService)
    {
    }

    /**
     * Add users to a project.
     *
     * @param Project $project
     * @param ProjectUserCreateRequest $request
     * @return JsonResponse
     */
    public function store(Project $project, ProjectUserCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['auth_id'] = auth()->guard('api')->id();

        $project = $this->projectUserService->createProjectUser($project, $data);

        return response()->json([
            'success' => true,
            'data' => new ProjectResource($project),
        ], 200);
    }

    /**
     * Remove a user from a project.
     *
     * @param Project $project
     * @param int $memberId
     * @return JsonResponse
     */
    public function destroy(Project $project, int $memberId): JsonResponse
    {
        $project = $this->projectUserService->removeProjectUser($project, $memberId);

        return response()->json([
            'success' => true,
            'message' => 'Successfully remove a member.',
            'data' => new ProjectResource($project),
        ], 200);
    }
}
