<?php

namespace App\Services;

use App\Repositories\TeamRepository;
use App\Repositories\TeamUserRepository;

class TeamUserService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private TeamRepository $teamRepository,
        private TeamUserRepository $teamUserRepository
    ) {
    }

    /**
     * Return the additional fields of pivot table.
     *
     * @param array $data
     * @return array
     */
    private function getPivotData(array $data): array
    {
        return [
            'created_by' => $data['auth_id'],
        ];
    }

    /**
     * Create team user.
     *
     * @param int $id
     * @param array $data
     * @return array
     */
    public function createTeamUser(int $id, array $data): array
    {
        $team = $this->teamRepository->getById($id);

        $data = array_fill_keys($data['member_id'], $this->getPivotData($data));

        $savedData = $this->teamUserRepository->create($team, $data);

        $memberIds = array_merge($savedData['attached'], $savedData['updated']);
        sort($memberIds);

        return $memberIds;
    }
}
