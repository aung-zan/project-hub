<?php

namespace App\Repositories;

use App\Models\Project;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ProjectUserRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Create/Update resources in the pivot table.
     *
     * @param Project $project
     * @param array $data
     * @return array
     */
    public function create(Project $project, array $data): array
    {
        return $project->users()->sync($data);
    }

    /**
     * Remove a resource from the pivot table.
     *
     * @param Project $project
     * @param int $id
     * @return void
     */
    public function delete(Project $project, int $id): void
    {
        $project->users()->detach($id);
    }

    /**
     * Check whether the resource exists in a pivot table or not.
     *
     * @param Project $team
     * @param int $userId
     * @return bool
     */
    public function userExists(Project $project, int $userId): bool
    {
        if (!$project->users()->wherePivot('user_id', $userId)) {
            throw new ModelNotFoundException('Resource not found.');
        }

        return true;
    }
}
