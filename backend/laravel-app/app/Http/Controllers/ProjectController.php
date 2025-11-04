<?php

namespace App\Http\Controllers;

use App\Http\Requests\Project\ProjectCreateRequest;
use App\Http\Requests\Project\ProjectIndexRequest;
use App\Http\Requests\Project\ProjectUpdateRequest;
use App\Models\Project;
use App\Services\ProjectService;

class ProjectController extends Controller
{
    public function __construct(private ProjectService $projectService)
    {
    }

    public function index(ProjectIndexRequest $request)
    {
        $data = $request->validated();
        $id = auth()->guard('api')->id();

        $projects = $this->projectService->getProjects($id, $data);

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

    public function show(Project $project)
    {
        $project = $this->projectService->getProject($project);

        return response()->json([
            'success' => true,
            'data' => $project,
        ]);
    }

    public function update(Project $project, ProjectUpdateRequest $request)
    {
        $data = $request->validated();

        $project = $this->projectService->updateProject($project, $data);

        return response()->json([
            'success' => true,
            'data' => $project,
        ]);
    }

    public function destroy(Project $project)
    {
        $project = $this->projectService->deleteProject($project);

        return response()->json([
            'success' => true,
            'data' => $project,
        ]);
    }
}
