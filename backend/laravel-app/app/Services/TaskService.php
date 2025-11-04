<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Database\Eloquent\Collection;

class TaskService
{
    /**
     * Create a new class instance.
     */
    public function __construct(private TaskRepository $taskRepo)
    {
        //
    }

    public function getTasks(): Collection
    {
        return $this->taskRepo->getAll();
    }

    public function createTask(array $data): Task
    {
        return $this->taskRepo->create($data);
    }

    public function getTask(Task $task): Task
    {
        return $task;
    }

    public function updateTask(Task $task, array $data): Task
    {
        return $this->taskRepo->update($task, $data);
    }

    public function deleteTask(Task $task): Task
    {
        return $this->taskRepo->delete($task);
    }
}
