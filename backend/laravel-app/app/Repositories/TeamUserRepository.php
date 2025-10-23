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

    /**
     * Remove a resource in the team_user table.
     *
     * @param Team $team
     * @param int $id
     * @return void
     */
    public function delete(Team $team, int $id): void
    {
        $team->users()->detach($id);
    }
}
