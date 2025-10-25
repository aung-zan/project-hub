<?php

namespace App\Repositories;

use App\Models\Team;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    /**
     * Check whether the value of the column is exists in pivot_table or not.
     *
     * @param Team $team
     * @param string $column
     * @param int $id
     * @return bool
     */
    public function checkExist(Team $team, string $column, int $id): bool
    {
        if (!$team->users()->wherePivot($column, $id)->exists()) {
            throw new ModelNotFoundException('Resource not found.');
        }

        return true;
    }
}
