<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TeamPolicy
{
    private $message = 'Resource not found.';
    private $code = 404;

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Team $team): Response
    {
        return $user->isMemberInTeam($team->id)
            ? Response::allow()
            : Response::deny($this->message, $this->code);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Team $team): Response
    {
        return $user->id === $team->created_by
            ? Response::allow()
            : Response::deny($this->message, $this->code);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Team $team): Response
    {
        return $user->id === $team->created_by
            ? Response::allow()
            : Response::deny($this->message, $this->code);
    }
}
