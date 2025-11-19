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
     * @return Project
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function createProjectUser(Project $project, array $data): Project
    {
        $data = $this->prepareForProjectUser($data);

        $this->projectUserRepo->create($project, $data);

        return $project->loadUsersWithSpecificColumns('users');
    }

    /**
     * Remove a user from a project.
     *
     * @param Project $project
     * @param int $memberId
     * @return Project
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function removeProjectUser(Project $project, int $memberId): Project
    {
        Gate::authorize('delete', $project);

        $this->projectUserRepo->userExists($project, $memberId);

        $this->projectUserRepo->delete($project, $memberId);

        return $project->loadUsersWithSpecificColumns('users');
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
