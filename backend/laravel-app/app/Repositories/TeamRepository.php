<?php

namespace App\Repositories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

class TeamRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(private Team $team)
    {
    }

    /**
     * Search, filter and sort the resources.
     *
     * @param array $filters
     * @return Collection
     */
    public function getAll(array $filters = []): Collection
    {
        $query = $this->team->query();

        if ($filters['created_by']) {
            $query = $query->where('created_by', $filters['created_by']);
        }

        return $query->get();
    }

    /**
     * Create a resource in a table.
     *
     * @param array $data
     * @return Team
     */
    public function create(array $data): Team
    {
        return $this->team->create($data);
    }

    /**
     * Find a resource with requested id.
     * if an id is not found, throws exception.
     *
     * @param int $id
     * @return Team
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById(int $id): Team
    {
        return $this->team->findOrFail($id);
    }

    /**
     * Update the resource.
     *
     * @param Team $team
     * @param array $data
     * @return Team
     */
    public function update(Team $team, array $data): Team
    {
        $team->update($data);

        return $team;
    }

    /**
     * Delete the resource.
     *
     * @param Team $team
     * @return Team
     */
    public function delete(Team $team): Team
    {
        $team->delete();

        return $team;
    }
}
