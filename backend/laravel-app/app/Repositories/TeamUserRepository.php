<?php

namespace App\Repositories;

use App\Models\Team;

class TeamUserRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
    }

    /**
     * Create/Update a resource in the team_user table.
     *
     * @param array $data
     * @return array
     */
    public function create(Team $team, array $data): array
    {
        return $team->users()->sync($data);
    }
}
