<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\ProjectCreateRequest;
use App\Http\Requests\Project\ProjectIndexRequest;
use App\Http\Requests\Project\ProjectUpdateRequest;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    public function __construct(private ProjectService $projectService)
    {
    }

    /**
     * List the projects.
     *
     * @param ProjectIndexRequest $request
     * @return JsonResponse
     */
    public function index(ProjectIndexRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = auth()->guard('api')->id();

        $projects = $this->projectService->getProjects($data);

        return response()->json([
            'success' => true,
            'data' => $projects,
        ]);
    }

    /**
     * Store a project data.
     *
     * @param ProjectCreateRequest $request
     * @return JsonResponse
     */
    public function store(ProjectCreateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = auth()->guard('api')->id();

        $project = $this->projectService->createProject($data);

        return response()->json([
            'success' => true,
            'data' => $project,
        ]);
    }

    /**
     * Show a project.
     *
     * @param Project $project
     * @return JsonResponse
     */
    public function show(Project $project): JsonResponse
    {
        $project = $this->projectService->getProject($project);

        return response()->json([
            'success' => true,
            'data' => $project,
        ]);
    }

    /**
     * Update a project data.
     *
     * @param Project $project
     * @param ProjectUpdateRequest $request
     * @return JsonResponse
     */
    public function update(Project $project, ProjectUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();

        $project = $this->projectService->updateProject($project, $data);

        return response()->json([
            'success' => true,
            'data' => $project,
        ]);
    }

    /**
     * Delete a project.
     *
     * @param Project $project
     * @return JsonResponse
     */
    public function destroy(Project $project): JsonResponse
    {
        $project = $this->projectService->deleteProject($project);

        return response()->json([
            'success' => true,
            'data' => $project,
        ]);
    }
}
