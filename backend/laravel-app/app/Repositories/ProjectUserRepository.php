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
     * Remove a resource in the pivot table.
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
     * Check whether the value of the column is exists in pivot_table or not.
     *
     * @param Project $team
     * @param string $column
     * @param int $id
     * @return bool
     */
    public function checkExist(Project $project, string $column, int $id): bool
    {
        if (!$project->users()->wherePivot($column, $id)->exists()) {
            throw new ModelNotFoundException('Resource not found.');
        }

        return true;
    }
}
