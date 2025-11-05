<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;

class TaskService
{
    /**
     * Create a new class instance.
     */
    public function __construct(private TaskRepository $taskRepo)
    {
        //
    }

    public function getTasks(array $data): Collection
    {
        Gate::authorize('view', [Task::class, $data['project_id']]);

        return $this->taskRepo->getAll();
    }

    public function createTask(array $data): Task
    {
        Gate::authorize('create', [Task::class, $data['project_id']]);

        return $this->taskRepo->create($data);
    }

    public function getTask(Task $task): Task
    {
        Gate::authorize('view', [Task::class, $task->project_id]);

        return $task;
    }

    public function updateTask(Task $task, array $data): Task
    {
        Gate::authorize('update', $task);

        return $this->taskRepo->update($task, $data);
    }

    public function deleteTask(Task $task): Task
    {
        Gate::authorize('delete', $task);

        return $this->taskRepo->delete($task);
    }
}
