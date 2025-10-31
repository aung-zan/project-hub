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
     * Create/Update resources in the pivot table.
     *
     * @param Team $team
     * @param array $data
     * @return array
     */
    public function create(Team $team, array $data): array
    {
        return $team->users()->sync($data);
    }

    /**
     * Remove a resource from the pivot table.
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
     * Check whether the resource exists in a pivot table or not.
     *
     * @param Team $team
     * @param int $userId
     * @return bool
     */
    public function userExists(Team $team, int $userId): bool
    {
        if (!$team->users()->wherePivot('user_id', $userId)->exists()) {
            throw new ModelNotFoundException('Resource not found.');
        }

        return true;
    }
}
