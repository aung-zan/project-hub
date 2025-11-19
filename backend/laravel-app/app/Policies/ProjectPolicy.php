<?php

namespace App\Policies;

use App\Enum\ProjectRoles;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
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
    public function view(User $user, Project $project): Response
    {
        return $user->isMemberInProject($project->id)
            ? Response::allow()
            : Response::deny($this->notFound['message'], $this->notFound['code']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): Response
    {
        $member = $user->getProjectMemberRole($project->id);

        if (!$member) {
            return Response::deny($this->notFound['message'], $this->notFound['code']);
        }

        return $member->role === ProjectRoles::Owner->value
            ? Response::allow()
            : Response::deny($this->notAuthorized['message'], $this->notAuthorized['code']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): Response
    {
        $member = $user->getProjectMemberRole($project->id);

        if (!$member) {
            return Response::deny($this->notFound['message'], $this->notFound['code']);
        }

        return $member->role === ProjectRoles::Owner->value
            ? Response::allow()
            : Response::deny($this->notAuthorized['message'], $this->notAuthorized['code']);
    }
}
