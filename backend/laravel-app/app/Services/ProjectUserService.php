<?php

namespace App\Services;

use App\Repositories\ProjectRepository;
use App\Repositories\ProjectUserRepository;
use Illuminate\Support\Facades\Gate;

class ProjectUserService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private ProjectRepository $projectRepo,
        private ProjectUserRepository $projectUserRepo
    ) {
    }

    /**
     * Add users to a project.
     *
     * @param int $projectId
     * @param array $data
     * @return array
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function createProjectUser(int $projectId, array $data): array
    {
        $project = $this->projectRepo->getById($projectId);

        Gate::authorize('view', $project);

        $data = array_fill_keys($data['member_id'], $this->getPivotData($data));

        $savedData = $this->projectUserRepo->create($project, $data);

        $memberIds = array_merge($savedData['attached'], $savedData['updated']);
        sort($memberIds);

        return $memberIds;
    }

    /**
     * Remove a user from a project.
     *
     * @param int $projectId
     * @param int $memberId
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function removeProjectUser(int $projectId, int $memberId): void
    {
        $project = $this->projectRepo->getById($projectId);

        Gate::authorize('view', $project);

        $this->projectUserRepo->userExists($project, $memberId);

        $this->projectUserRepo->delete($project, $memberId);
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
