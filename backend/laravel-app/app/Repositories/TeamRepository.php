<?php

namespace App\Repositories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

class TeamRepository
{
    private Team $team;

    /**
     * Create a new class instance.
     */
    public function __construct(Team $team)
    {
        $this->team = $team;
    }

    /**
     * Get all the resources filtered by $filters.
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
     * Create a resource in the team table.
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
     * @return ?Team
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getById(int $id): ?Team
    {
        return $this->team->findOrFail($id);
    }

    /**
     * Find a resource with requested id and update it.
     * if an id is not found, throws exception.
     *
     * @param int $id
     * @param array $data
     * @return ?Team
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function update(int $id, array $data): ?Team
    {
        $team = $this->getById($id);

        $team->update($data);

        return $team;
    }

    /**
     * Find a resouce with requested id and delete it.
     * if an id is not found, throws exception.
     *
     * @param int $id
     * @return ?Team
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function delete(int $id): ?Team
    {
        $team = $this->getById($id);

        $team->delete();

        return $team;
    }
}
