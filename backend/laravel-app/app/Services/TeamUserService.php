<?php

namespace App\Services;

use App\Repositories\TeamRepository;
use App\Repositories\TeamUserRepository;
use Illuminate\Support\Facades\Gate;

class TeamUserService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private TeamRepository $teamRepo,
        private TeamUserRepository $teamUserRepo,
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
     * @return ?array
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function createTeamUser(int $id, array $data): ?array
    {
        $team = $this->teamRepo->getById($id);

        Gate::authorize('view', $team);

        $data = array_fill_keys($data['member_id'], $this->getPivotData($data));

        $savedData = $this->teamUserRepo->create($team, $data);

        $memberIds = array_merge($savedData['attached'], $savedData['updated']);
        sort($memberIds);

        return $memberIds;
    }

    /**
     * Remove a user from a team.
     *
     * @param int $teamId
     * @param int $memberId
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function removeTeamUser(int $teamId, int $memberId): void
    {
        $team = $this->teamRepo->getById($teamId);

        Gate::authorize('view', $team);

        $this->teamUserRepo->checkExist($team, 'user_id', $memberId);

        $this->teamUserRepo->delete($team, $memberId);
    }
}
