<?php

namespace App\Services;

use App\Models\Team;
use App\Repositories\TeamRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;

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

    /**
     * Find and filtered the resources.
     *
     * @return Collection
     */
    public function getAllTeam(): Collection
    {
        $filters = [
            'created_by' => auth()->guard('api')->id(),
        ];

        return $this->teamRepository->getAll($filters);
    }

    /**
     * Create the resource.
     *
     * @param array $data
     * @return Team
     */
    public function createTeam(array $data): Team
    {
        return $this->teamRepository->create($data);
    }

    /**
     * Find the resource and check the authorization.
     *
     * @param int $id
     * @return ?Team
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function getTeam(int $id): ?Team
    {
        $team = $this->teamRepository->getById($id);

        Gate::authorize('view', $team);

        return $team;
    }

    /**
     * Find the resource, check the authorization and update it.
     *
     * @param int $id
     * @param array $data
     * @return ?Team
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     *
     */
    public function updateTeam(int $id, array $data): ?Team
    {
        $team = $this->teamRepository->getById($id);

        Gate::authorize('update', $team);

        return $this->teamRepository->update($team, $data);
    }

    /**
     * Find the resource, check the authorization and delete the resource.
     *
     * @param int $id
     * @return ?Team
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function deleteTeam(int $id): ?Team
    {
        $team = $this->teamRepository->getById($id);

        Gate::authorize('delete', $team);

        return $this->teamRepository->delete($team);
    }
}
