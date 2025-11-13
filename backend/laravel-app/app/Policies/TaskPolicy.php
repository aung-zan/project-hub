<?php

namespace App\Policies;

use App\Enum\ProjectRoles;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    private $notFound = [
        'message' => 'Resource not found.',
        'code' => 404,
    ];

    private $notAuthorized = [
        'message' => 'This action is unauthorized.',
        'code' => 401,
    ];

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, int $projectId): Response
    {
        return $user->isMemberInProject($projectId)
            ? Response::allow()
            : Response::deny($this->notFound['message'], $this->notFound['code']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, int $projectId): Response
    {
        $membership = $user->projectMembership($projectId);

        return !$membership || $membership->role === ProjectRoles::Viewer->value
            ? Response::deny($this->notAuthorized['message'], $this->notAuthorized['code'])
            : Response::allow();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): Response
    {
        $membership = $user->projectMembership($task->project_id);

        if (!$membership) {
            return Response::deny($this->notFound['message'], $this->notFound['code']);
        }

        $hasOwnerRole = $membership->role === ProjectRoles::Owner->value;
        $isOwned = $membership->role === ProjectRoles::Member->value && $task->created_by === $user->id;

        return $hasOwnerRole || $isOwned
            ? Response::allow()
            : Response::deny($this->notAuthorized['message'], $this->notAuthorized['code']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): Response
    {
        $membership = $user->projectMembership($task->project_id);

        if (!$membership) {
            return Response::deny($this->notFound['message'], $this->notFound['code']);
        }

        $hasOwnerRole = $membership->role === ProjectRoles::Owner->value;
        $isOwned = $membership->role === ProjectRoles::Member->value && $task->created_by === $user->id;

        return $hasOwnerRole || $isOwned
            ? Response::allow()
            : Response::deny($this->notAuthorized['message'], $this->notAuthorized['code']);
    }
}
