<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\ProjectRepository;
use App\Repositories\ProjectUserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;

class ProjectService
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
     * Find and filtered the resources.
     *
     * @param int $id
     * @param array $data
     * @return Collection
     */
    public function getProjects(int $id, array $data): Collection
    {
        $search = '';
        $filters['created_by'] = $id;

        if (array_key_exists('search', $data)) {
            $search = $data['search'];
        }

        if (array_key_exists('status', $data)) {
            $filters['status'] = $data;
        }

        if (array_key_exists('start', $data)) {
            # code...
        }

        if (array_key_exists('end', $data)) {
            # code...
        }

        if (array_key_exists('sort', $data)) {
            list($column, $direction) = explode('_', $data['sort']);

            switch ($column) {
                case 'start':
                    $sort['start_date'] = $direction;
                    break;

                case 'end':
                    $sort['end_date'] = $direction;
                    break;

                default:
                    $sort[$column] = $direction;
                    break;
            }
        } else {
            $sort['id'] = 'asc';
        }

        return $this->projectRepo->getAll($search, $filters, $sort);
    }

    /**
     * Create the resource.
     *
     * @param array $data
     * @return Project
     */
    public function createProject(array $data): Project
    {
        $project = $this->projectRepo->create($data);

        // for pivot data
        $member = [$data['created_by'] => [
            // pivot additional info
            'created_by' => $data['created_by'],
            'role' => 'owner'
        ]];

        $this->projectUserRepo->create($project, $member);

        return $project;
    }

    /**
     * Find the resource and check the authorization.
     *
     * @param Project $project
     * @return Project
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function getProject(Project $project): Project
    {
        Gate::authorize('view', $project);

        return $project;
    }

    /**
     * Find the resource, check the authorization and update it.
     *
     * @param Project $project
     * @param array $data
     * @return Project
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     *
     */
    public function updateProject(Project $project, array $data): Project
    {
        Gate::authorize('update', $project);

        return $this->projectRepo->update($project, $data);
    }

    /**
     * Find the resource, check the authorization and delete it.
     *
     * @param Project $project
     * @return Project
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function deleteProject(Project $project): Project
    {
        Gate::authorize('delete', $project);

        return $this->projectRepo->delete($project);
    }
}
