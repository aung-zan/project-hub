<?php

namespace App\Repositories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Collection;

class ProjectRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(private Project $project)
    {
    }

    /**
     * Search, filter and sort the resources.
     *
     * @param string $search
     * @param array $filters
     * @param array $sort
     * @return Collection
     */
    public function getAll(string $search, array $filters, array $sort): Collection
    {
        $query = $this->project->query();

        $query = $query->memberProjects($filters['user_id']);

        if ($search) {
            /**
             * dynamic local scopes.
             */
            $query = $query->searchWith($search);

            $query = $query->orderWith($search);
        }

        if (array_key_exists('status', $filters)) {
            $query = $query->where('status', $filters['status']);
        }

        foreach ($sort as $column => $direction) {
            $query = $query->orderBy($column, $direction);
        }

        return $query->get();
    }

    /**
     * Create a resource.
     *
     * @param array $data
     * @return Project
     */
    public function create(array $data): Project
    {
        return $this->project->create($data);
    }

    /**
     * Update the resource.
     *
     * @param Project $project
     * @param array $data
     * @return Project
     */
    public function update(Project $project, array $data): Project
    {
        $project->update($data);

        return $project;
    }

    /**
     * Delete the resource.
     *
     * @param Project $project
     * @return Project
     */
    public function delete(Project $project): Project
    {
        $project->delete();

        return $project;
    }
}
