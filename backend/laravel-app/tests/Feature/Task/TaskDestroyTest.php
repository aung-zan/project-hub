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

class TaskDestroyTest extends FeatureTestCase
{
    use RefreshDatabase;
    use TestHelper;

    protected string $method = 'delete';
    protected string $url = 'http://localhost/api/tasks/{taskId}';

    private array $projectData = [
        'name' => 'testing',
        'status' => 'active',
    ];
    private array $search = ['{taskId}'];

    /**
     * Test for user cannot delete a resource with wrong id.
     */
    public function testUserCannotDeleteAResourceWithWrongId(): void
    {
        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->getRealURL($this->search, [1]));

        $response->assertStatus(404)
            ->assertJsonFragments([
                ['success' => false,],
                ['message' => 'Resource not found.'],
            ]);
    }

    /**
     * Test for user cannot delete a resource without role.
     */
    public function testUserCannotDeleteAResourceWithoutRole(): void
    {
        $user = User::factory()->create();

        $projectData = $this->projectData;
        $projectData['created_by'] = $user->id;
        $project = Project::factory()->create($projectData);

        $taskData = [
            'project_id' => $project->id,
            'assigned_to' => $user->id,
            'created_by' => $user->id,
        ];
        $task = Task::factory()->create($taskData);

        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->getRealURL($this->search, [$task->id]));

        $response->assertStatus(404)
            ->assertJsonFragments([
                ['success' => false],
                ['message' => 'Resource not found.'],
            ]);
    }

    /**
     * Test for user cannot delete a resource with viewer role.
     */
    public function testUserCannotDeleteAResourceWithViewerRole(): void
    {
        list($token, $userId) = $this->login();

        $projectData = $this->projectData;
        $projectData['created_by'] = $userId;
        $project = Project::factory()->create($projectData);

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $userId,
            'role' => 'viewer',
            'created_by' => $userId
        ]);

        $taskData = [
            'project_id' => $project->id,
            'assigned_to' => $userId,
            'created_by' => $userId,
        ];
        $task = Task::factory()->create($taskData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->getRealURL($this->search, [$task->id]));

        $response->assertStatus(401)
            ->assertJsonFragments([
                ['success' => false],
                ['message' => 'This action is unauthorized.'],
            ]);
    }

    /**
     * Test for user can delete a resoruce with member role and unauthorized id.
     */
    public function testUserCannotDeleteAResourceWithMemberRoleAndUnauthorizedId(): void
    {
        $user = User::factory()->create();

        $projectData = $this->projectData;
        $projectData['created_by'] = $user->id;
        $project = Project::factory()->create($projectData);

        $taskData = [
            'project_id' => $project->id,
            'assigned_to' => $user->id,
            'created_by' => $user->id,
        ];
        $task = Task::factory()->create($taskData);

        list($token, $userId) = $this->login();

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $userId,
            'role' => 'member',
            'created_by' => $user->id
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->getRealURL($this->search, [$task->id]));

        $response->assertStatus(401)
            ->assertJsonFragments([
                ['success' => false],
                ['message' => 'This action is unauthorized.'],
            ]);
    }

    /**
     * Test for user can delete a resoruce with member role and authorized id.
     */
    public function testUserCanDeleteAResourceWithMemberRoleAndAuthorizedId(): void
    {
        list($token, $userId) = $this->login();

        $projectData = $this->projectData;
        $projectData['created_by'] = $userId;
        $project = Project::factory()->create($projectData);

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $userId,
            'role' => 'member',
            'created_by' => $userId
        ]);

        $taskData = [
            'project_id' => $project->id,
            'assigned_to' => $userId,
            'created_by' => $userId,
        ];
        $task = Task::factory()->create($taskData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->getRealURL($this->search, [$task->id]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'project_id' => $taskData['project_id'],
                    'assigned_to' => $taskData['assigned_to'],
                    'created_by' => $taskData['created_by'],
                ],
            ]);

        $this->assertDatabaseMissing('tasks', $taskData);
    }

    /**
     * Test for user can delete a resource with owner role.
     */
    public function testUserCanDeleteAResourceWithOwnerRole(): void
    {
        list($token, $userId) = $this->login();

        $projectData = $this->projectData;
        $projectData['created_by'] = $userId;
        $project = Project::factory()->create($projectData);

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $userId,
            'role' => 'owner',
            'created_by' => $userId
        ]);

        $taskData = [
            'project_id' => $project->id,
            'assigned_to' => $userId,
            'created_by' => $userId,
        ];
        $task = Task::factory()->create($taskData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->getRealURL($this->search, [$task->id]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'project_id' => $taskData['project_id'],
                    'assigned_to' => $taskData['assigned_to'],
                    'created_by' => $taskData['created_by'],
                ]
            ]);

        $this->assertDatabaseMissing('tasks', $taskData);
    }
}
