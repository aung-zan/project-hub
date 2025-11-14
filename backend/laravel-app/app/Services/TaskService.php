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

    /**
     * Check the authorization, find and filtered the resources.
     *
     * @param array $data
     * @return Collection
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function getTasks(array $data): Collection
    {
        Gate::authorize('view', [Task::class, $data['project_id']]);

        return $this->taskRepo->getAll();
    }

    /**
     * Check the authorization and create the resource.
     *
     * @param array $data
     * @return Task
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function createTask(array $data): Task
    {
        return $this->taskRepo->create($data);
    }

    /**
     * Check the authorization of the resource.
     *
     * @param Task $task
     * @return Task
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function getTask(Task $task): Task
    {
        Gate::authorize('view', [Task::class, $task->project_id]);

        return $task;
    }

    /**
     * Check the authorization of the resource and update it.
     *
     * @param Task $task
     * @param array $data
     * @return Task
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function updateTask(Task $task, array $data): Task
    {
        return $this->taskRepo->update($task, $data);
    }

    /**
     * Check the authorization of the resource and delete it.
     *
     * @param Task $task
     * @return Task
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function deleteTask(Task $task): Task
    {
        Gate::authorize('delete', $task);

        return $this->taskRepo->delete($task);
    }
}
