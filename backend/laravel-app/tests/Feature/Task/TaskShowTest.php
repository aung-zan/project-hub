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
use Tests\TestCase;

class TaskShowTest extends FeatureTestCase
{
    use RefreshDatabase;
    use TestHelper;

    protected string $method = 'get';
    protected string $url = 'http://localhost/api/tasks/{taskId}';

    private array $projectData = [
        'name' => 'testing',
        'status' => 'active',
    ];
    private array $search = ['{taskId}'];

    /**
     * Test for user cannot get a resource with wrong id.
     */
    public function testUserCannotGetAResourceWithWrongId(): void
    {
        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson($this->getRealURL($this->search, [1]));

        $response->assertStatus(404)
            ->assertJsonFragments([
                ['success' => false],
                ['message' => 'Resource not found.'],
            ]);
    }

    /**
     * Test for user cannot get a resource without role.
     */
    public function testUserCannotGetAResourceWithoutRole(): void
    {
        $user = User::factory()->create();

        $projectData = $this->projectData;
        $projectData['created_by'] = $user->id;
        $project = Project::factory()->create($projectData);

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'role' => 'owner',
            'created_by' => $user->id,
        ]);

        $taskData = [
            'project_id' => $project->id,
            'assigned_to' => $user->id,
            'created_by' => $user->id,
        ];
        $task = Task::factory()->create($taskData);

        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson($this->getRealURL($this->search, [$task->id]));

        $response->assertStatus(404)
            ->assertJsonFragments([
                ['success' => false],
                ['message' => 'Resource not found.'],
            ]);
    }

    /**
     * Test for user can get a resource with any role.
     */
    public function testUserCanGetAResourceWithAnyRole(): void
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
        $task = Task::factory()->create($taskData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson($this->getRealURL($this->search, [$task->id]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'project_id' => $taskData['project_id'],
                    'assigned_to' => $taskData['assigned_to'],
                    'created_by' => $taskData['created_by'],
                ],
            ]);
    }
}
