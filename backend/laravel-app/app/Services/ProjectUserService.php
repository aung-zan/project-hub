<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\ProjectUserRepository;
use Illuminate\Support\Facades\Gate;

class ProjectUserService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        private ProjectUserRepository $projectUserRepo
    ) {
    }

    /**
     * Add users to a project.
     *
     * @param Project $project
     * @param array $data
     * @return array
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function createProjectUser(Project $project, array $data): array
    {
        $data = $this->prepareForProjectUser($data);

        $savedData = $this->projectUserRepo->create($project, $data);

        $memberIds = array_merge($savedData['attached'], $savedData['updated']);
        sort($memberIds);

        return $memberIds;
    }

    /**
     * Remove a user from a project.
     *
     * @param Project $project
     * @param int $memberId
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function removeProjectUser(Project $project, int $memberId): void
    {
        Gate::authorize('delete', $project);

        $this->projectUserRepo->userExists($project, $memberId);

        $this->projectUserRepo->delete($project, $memberId);
    }

    /**
     * Return the additional fields of pivot table.
     *
     * @param array $data
     * @return array
     */
    private function prepareForProjectUser(array $data): array
    {
        $result = [];
        $authId = $data['auth_id'];
        sort($data['members']);

        foreach ($data['members'] as $member) {
            $result[$member['id']] = [
                'role' => $member['role'],
                'created_by' => $authId,
            ];
        }

        return $result;
    }
}
