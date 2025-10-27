<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\ProjectCreateRequest;
use App\Http\Requests\Project\ProjectUpdateRequest;
use App\Services\ProjectService;

class ProjectController extends Controller
{
    public function __construct(private ProjectService $projectService)
    {
    }

    public function index()
    {
        $id = auth()->guard('api')->id();
        $projects = $this->projectService->getAllProject($id);

        return response()->json([
            'success' => true,
            'data' => $projects,
        ]);
    }

    public function store(ProjectCreateRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->guard('api')->id();

        $project = $this->projectService->createProject($data);

        return response()->json([
            'success' => true,
            'data' => $project,
        ]);
    }

    public function show(int $id)
    {
        $project = $this->projectService->getProject($id);

        return response()->json([
            'success' => true,
            'data' => $project,
        ]);
    }

    public function update(int $id, ProjectUpdateRequest $request)
    {
        $data = $request->toArray();

        $project = $this->projectService->updateProject($id, $data);

        return response()->json([
            'success' => true,
            'data' => $project,
        ]);
    }

    public function destroy(int $id)
    {
        $project = $this->projectService->deleteProject($id);

        return response()->json([
            'success' => true,
            'data' => $project,
        ]);
    }
}
