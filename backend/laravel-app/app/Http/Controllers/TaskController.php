<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService)
    {
        //
    }

    public function index(Project $project, Request $request)
    {
        $data = $request->toArray();
        $data['created_by'] = auth()->guard('api')->id();

        $task = $this->taskService->getTasks();

        return response()->json([
            'success' => true,
            'data' => $task,
        ]);
    }

    public function store(Project $project, Request $request)
    {
        $data = $request->toArray();
        $data['project_id'] = $project->id;
        $data['created_by'] = auth()->guard('api')->id();

        $task = $this->taskService->createTask($data);

        return response()->json([
            'success' => true,
            'data' => $task,
        ]);
    }

    public function show(Task $task)
    {
        $task = $this->taskService->getTask($task);

        return response()->json([
            'success' => true,
            'data' => $task,
        ]);
    }

    public function update(Task $task, Request $request)
    {
        $data = $request->toArray();

        $task = $this->taskService->updateTask($task, $data);

        return response()->json([
            'success' => true,
            'data' => $task,
        ]);
    }

    public function destroy(Task $task)
    {
        $task = $this->taskService->deleteTask($task);

        return response()->json([
            'success' => true,
            'data' => $task,
        ]);
    }
}
