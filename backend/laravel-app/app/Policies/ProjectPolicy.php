<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    private $message = 'Resource not found.';
    private $code = 404;

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): Response
    {
        return $user->id === $project->created_by
            ? Response::allow()
            : Response::deny($this->message, $this->code);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): Response
    {
        return $user->id === $project->created_by
            ? Response::allow()
            : Response::deny($this->message, $this->code);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): Response
    {
        return $user->id === $project->created_by
            ? Response::allow()
            : Response::deny($this->message, $this->code);
    }
}
