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
     * Get all the resources filtered by $filters.
     *
     * @param array $filters
     * @return Collection
     */
    public function getAll(array $filters = []): Collection
    {
        $query = $this->project->query();

        if ($filters['created_by']) {
            $query = $query->where('created_by', $filters['created_by']);
        }

        $query = $query->orderBy('id');

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
