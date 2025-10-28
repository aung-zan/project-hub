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

        if ($search) {
            /**
             * dynamic local scopes.
             */
            $query = $query->searchWith($search);

            $query = $query->orderWith($search);
        }

        foreach ($filters as $column => $value) {
            $query = $query->where($column, $value);
        }

        foreach ($sort as $column => $direction) {
            $query = $query->orderBy($column, $direction);
        }

        return $query->get();
    }

    /**
     * Create a resource in the project table.
     *
     * @param array $data
     * @return Project
     */
    public function create(array $data): Project
    {
        return $this->project->create($data);
    }

    /**
     * Find a resource with requested id.
     * if an id is not found, throws exception.
     *
     * @param int $id
     * @return Project
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById(int $id): Project
    {
        return $this->project->findOrFail($id);
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
