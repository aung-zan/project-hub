<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\ProjectRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;

class ProjectService
{
    /**
     * Create a new class instance.
     */
    public function __construct(private ProjectRepository $projectRepo)
    {
    }

    /**
     * Find and filtered the resources.
     *
     * @return Collection
     */
    public function getAllProject(int $id): Collection
    {
        $filters = [
            'created_by' => $id,
        ];

        return $this->projectRepo->getAll($filters);
    }

    /**
     * Create the resource.
     *
     * @param array $data
     * @return Project
     */
    public function createProject(array $data): Project
    {
        return $this->projectRepo->create($data);
    }

    /**
     * Find the resource and check the authorization.
     *
     * @param int $id
     * @return Project
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function getProject(int $id): Project
    {
        $project = $this->projectRepo->getById($id);

        Gate::authorize('view', $project);

        return $project;
    }

    /**
     * Find the resource, check the authorization and update it.
     *
     * @param int $id
     * @param array $data
     * @return Project
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     *
     */
    public function updateProject(int $id, array $data): Project
    {
        $project = $this->projectRepo->getById($id);

        Gate::authorize('update', $project);

        return $this->projectRepo->update($project, $data);
    }

    /**
     * Find the resource, check the authorization and delete the resource.
     *
     * @param int $id
     * @return Project
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function deleteProject(int $id): Project
    {
        $project = $this->projectRepo->getById($id);

        Gate::authorize('delete', $project);

        return $this->projectRepo->delete($project);
    }
}
