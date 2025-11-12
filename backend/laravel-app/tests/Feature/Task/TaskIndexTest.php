<?php

namespace Tests\Feature\Task;

use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\FeatureTestCase;

class TaskIndexTest extends FeatureTestCase
{
    use RefreshDatabase;
    use TestHelper;

    protected string $method = 'get';
    protected string $url = 'http://localhost/api/projects/{projectId}/tasks';

    private array $projectData = [
        'name' => 'testing',
        'status' => 'active',
    ];
    private array $search = ['{projectId}'];

    /**
     * Test for user cannot access the data with right token and without role.
     */
    public function testUserCannotAccessIndexWithRightTokenAndWithoutRole(): void
    {
        $user = User::factory()->create();

        $projectData = $this->projectData;
        $projectData['created_by'] = $user->id;
        $project = Project::factory()->create($projectData);

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'role' => 'owner',
            'created_by' => $user->id
        ]);

        $taskData = [
            'project_id' => $project->id,
            'assigned_to' => $user->id,
            'created_by' => $user->id,
        ];
        Task::factory()->create($taskData);

        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson($this->getRealURL($this->search, [$project->id]));

        $response->assertStatus(404)
            ->assertJsonFragments([
                ['success' => false],
                ['message' => 'Resource not found.'],
            ]);
    }

    /**
     * Test for user can access the data with right token and with any role.
     */
    public function testUserCanAccessIndexWithRightTokenAndWithAnyRole(): void
    {
        $user = User::factory()->create();

        $projectData = $this->projectData;
        $projectData['created_by'] = $user->id;
        $project = Project::factory()->create($projectData);

        list($token, $userId) = $this->login();

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $userId,
            'role' => 'viewer',
            'created_by' => $user->id,
        ]);

        $taskData = [
            'project_id' => $project->id,
            'assigned_to' => $user->id,
            'created_by' => $user->id,
        ];
        Task::factory()->create($taskData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson($this->getRealURL($this->search, [$project->id]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    [
                        'project_id' => $taskData['project_id'],
                        'assigned_to' => $taskData['assigned_to'],
                        'created_by' => $taskData['created_by'],
                    ],
                ],
            ]);
    }
}
