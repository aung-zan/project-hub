<?php

namespace App\Services;

use App\Models\Team;
use App\Repositories\TeamUserRepository;
use Illuminate\Support\Facades\Gate;

class TeamUserService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private TeamUserRepository $teamUserRepo,
    ) {
    }

    /**
     * Add users to a team.
     *
     * @param Team $team
     * @param array $data
     * @return array
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function createTeamUser(Team $team, array $data): array
    {
        $data = array_fill_keys($data['member_id'], $this->getPivotData($data));

        $savedData = $this->teamUserRepo->create($team, $data);

        $memberIds = array_merge($savedData['attached'], $savedData['updated']);
        sort($memberIds);

        return $memberIds;
    }

    /**
     * Remove a user from a team.
     *
     * @param Team $team
     * @param int $memberId
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function removeTeamUser(Team $team, int $memberId): void
    {
        Gate::authorize('delete', $team);

        $this->teamUserRepo->userExists($team, $memberId);

        $this->teamUserRepo->delete($team, $memberId);
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
}
