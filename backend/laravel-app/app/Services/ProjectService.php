<?php

namespace App\Services;

use App\Models\Project;
use App\Repositories\ProjectRepository;
use App\Repositories\ProjectUserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
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
     * Find and filtered the projects that a user belongs to.
     *
     * @param array $data
     * @return Collection
     */
    public function getProjects(array $data): Collection
    {
        $search = '';
        $filters['user_id'] = $data['user_id'];

        if (array_key_exists('search', $data)) {
            $search = $data['search'];
        }

        if (array_key_exists('status', $data)) {
            $filters['status'] = $data['status'];
        }

        // TODO: implement date search.
        if (array_key_exists('start', $data)) {
            # code...
        }

        if (array_key_exists('end', $data)) {
            # code...
        }

        $sort = $this->createSortData($data);

        return $this->projectRepo->getAll($search, $filters, $sort);
    }

    /**
     * Create a project.
     *
     * @param array $data
     * @return Project
     */
    public function createProject(array $data): Project
    {
        return DB::transaction(function () use ($data) {
            $project = $this->projectRepo->create($data);

            // for pivot data
            $member = [$data['created_by'] => [
                // pivot additional info
                'created_by' => $data['created_by'],
                'role' => 'owner'
            ]];

            $this->projectUserRepo->create($project, $member);

            return $project;
        });
    }

    /**
     * Find a project that a user belongs to.
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

        return $project->loadUsersWithSpecificColumns();
    }

    /**
     * Update a project that a user belongs to.
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
        return $this->projectRepo->update($project, $data);
    }

    /**
     * Delete a project that a user belongs to.
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

    /**
     * Create a sort data from the request data.
     *
     * @param array $data
     * @return array
     */
    private function createSortData(array $data): array
    {
        if (array_key_exists('sort', $data)) {
            list($column, $direction) = explode('-', $data['sort']);

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

            return $sort;
        }

        return ['id' => 'asc'];
    }
}
