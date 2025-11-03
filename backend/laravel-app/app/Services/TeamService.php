<?php

namespace App\Services;

use App\Models\Team;
use App\Repositories\TeamRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;

class TeamService
{
    /**
     * Create a new class instance.
     */
    public function __construct(private TeamRepository $teamRepo)
    {
    }

    /**
     * Find and filtered the resources.
     *
     * @return Collection
     */
    public function getAllTeam(): Collection
    {
        // TODO: mixed with request in controller and clean here.
        $filters = [
            'created_by' => auth()->guard('api')->id(),
        ];

        return $this->teamRepo->getAll($filters);
    }

    /**
     * Create the resource.
     *
     * @param array $data
     * @return Team
     */
    public function createTeam(array $data): Team
    {
        return $this->teamRepo->create($data);
    }

    /**
     * Find the resource and check the authorization.
     *
     * @param Team $team
     * @return Team
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function getTeam(Team $team): Team
    {
        Gate::authorize('view', $team);

        return $team;
    }

    /**
     * Find the resource, check the authorization and update it.
     *
     * @param Team $team
     * @param array $data
     * @return Team
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     *
     */
    public function updateTeam(Team $team, array $data): Team
    {
        Gate::authorize('update', $team);

        return $this->teamRepo->update($team, $data);
    }

    /**
     * Find the resource, check the authorization and delete it.
     *
     * @param Team $team
     * @return Team
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function deleteTeam(Team $team): Team
    {
        Gate::authorize('delete', $team);

        return $this->teamRepo->delete($team);
    }
}
