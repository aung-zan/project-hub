<?php

namespace App\Services;

use App\Repositories\TeamRepository;

class TeamService
{
    private TeamRepository $teamRepository;

    /**
     * Create a new class instance.
     */
    public function __construct(TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    public function getAllTeam()
    {
        $filters = [
            'created_by' => auth()->guard('api')->id(),
        ];

        return $this->teamRepository->getAll($filters);
    }

    public function createTeam(array $data)
    {
        return $this->teamRepository->create($data);
    }

    public function getTeam(int $id)
    {
        return $this->teamRepository->getById($id);
    }

    public function updateTeam(int $id, array $data)
    {
        return $this->teamRepository->update($id, $data);
    }

    public function deleteTeam(int $id)
    {
        return $this->teamRepository->delete($id);
    }
}
