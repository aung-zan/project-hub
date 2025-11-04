<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(private Task $task)
    {
        //
    }

    public function getAll(): Collection
    {
        $query = $this->task->query();

        return $query->get();
    }

    public function create(array $data): Task
    {
        return $this->task->create($data);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task;
    }

    public function delete(Task $task): Task
    {
        $task->delete();

        return $task;
    }
}
