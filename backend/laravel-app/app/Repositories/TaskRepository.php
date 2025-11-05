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

    /**
     * Search, filter and sort the resources.
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        $query = $this->task->query();

        return $query->get();
    }

    /**
     * Create a resource in a table.
     *
     * @param array $data
     * @return Task
     */
    public function create(array $data): Task
    {
        return $this->task->create($data);
    }

    /**
     * Update the resource.
     *
     * @param Task $task
     * @param array $data
     * @return Task
     */
    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task;
    }

    /**
     * Delete the resource.
     *
     * @param Task $task
     * @return Task
     */
    public function delete(Task $task): Task
    {
        $task->delete();

        return $task;
    }
}
