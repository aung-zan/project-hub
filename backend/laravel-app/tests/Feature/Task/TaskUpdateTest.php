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

class TaskUpdateTest extends FeatureTestCase
{
    use RefreshDatabase;
    use TestHelper;

    protected string $method = 'put';
    protected string $url = 'http://localhost/api/tasks/{taskId}';

    private array $search = ['{taskId}'];
    private array $taskData = [
        'title' => 'testing',
        'status' => 'todo',
    ];
    private array $request = [
        'title' => 'another testing',
        'status' => 'review',
    ];

    /**
     * Test for user cannot update a resource with wrong id.
     */
    public function testUserCannotUpdateAResourceWithWrongId(): void
    {
        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->getRealURL($this->search, [10]));

        $response->assertStatus(404)
            ->assertJsonFragments([
                ['success' => false],
                ['message' => 'Resource not found.'],
            ]);
    }

    /**
     * Test for user cannot update a resource without role.
     */
    public function testUserCannotUpdateAResourceWithoutRole(): void
    {
        $user = User::factory()->create();

        $projectData['created_by'] = $user->id;
        $project = Project::factory()->create($projectData);

        $taskData = $this->taskData;
        $taskData['project_id'] = $project->id;
        $taskData['assigned_to'] = $user->id;
        $taskData['created_by'] = $user->id;
        $task = Task::factory()->create($taskData);

        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->getRealURL($this->search, [$task->id]));

        $response->assertStatus(404)
            ->assertJsonFragments([
                ['success' => false],
                ['message' => 'Resource not found.'],
            ]);
    }

    /**
     * Test for user cannot update a resource with viewer role.
     */
    public function testUserCannotUpdateAResourceWithViewerRole(): void
    {
        $user = User::factory()->create();

        $projectData['created_by'] = $user->id;
        $project = Project::factory()->create($projectData);

        list($token, $userId) = $this->login();

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $userId,
            'role' => 'viewer',
            'created_by' => $user->id,
        ]);

        $taskData = $this->taskData;
        $taskData['project_id'] = $project->id;
        $taskData['assigned_to'] = $user->id;
        $taskData['created_by'] = $user->id;
        $task = Task::factory()->create($taskData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->getRealURL($this->search, [$task->id]));

        $response->assertStatus(401)
            ->assertJsonFragments([
                ['success' => false],
                ['message' => 'This action is unauthorized.'],
            ]);
    }

    /**
     * Test for user cannot update a resource with member role and unauthorized id.
     */
    public function testUserCannotUpdateAResourceWithMemeberRoleAndUnauthorizedId(): void
    {
        $user = User::factory()->create();

        $projectData['created_by'] = $user->id;
        $project = Project::factory()->create($projectData);

        list($token, $userId) = $this->login();

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $userId,
            'role' => 'member',
            'created_by' => $user->id,
        ]);

        $taskData = $this->taskData;
        $taskData['project_id'] = $project->id;
        $taskData['assigned_to'] = $user->id;
        $taskData['created_by'] = $user->id;
        $task = Task::factory()->create($taskData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->getRealURL($this->search, [$task->id]));

        $response->assertStatus(401)
            ->assertJsonFragments([
                ['success' => false],
                ['message' => 'This action is unauthorized.'],
            ]);
    }

    /**
     * Test for user can update a resource with member role and authorized id.
     */
    public function testUserCanUpdateAResourceWithMemberRoleAndAuthorizedId(): void
    {
        $user = User::factory()->create();

        $projectData['created_by'] = $user->id;
        $project = Project::factory()->create($projectData);

        list($token, $userId) = $this->login();

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $userId,
            'role' => 'member',
            'created_by' => $user->id,
        ]);

        $taskData = $this->taskData;
        $taskData['project_id'] = $project->id;
        $taskData['assigned_to'] = $user->id;
        $taskData['created_by'] = $userId;
        $task = Task::factory()->create($taskData);

        $request = $this->request;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->getRealURL($this->search, [$task->id]), $request);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => $request['title'],
                    'status' => $request['status'],
                ],
            ]);

        $this->assertDatabaseMissing('tasks', $taskData);
        $this->assertDatabaseHas('tasks', $request);
    }

    /**
     * Test for userr can update a resource with owner role.
     */
    public function testUserCanUpdateAResourceWithOwnerRole(): void
    {
        $user = User::factory()->create();

        $projectData['created_by'] = $user->id;
        $project = Project::factory()->create($projectData);

        list($token, $userId) = $this->login();

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $userId,
            'role' => 'owner',
            'created_by' => $user->id,
        ]);

        $taskData = $this->taskData;
        $taskData['project_id'] = $project->id;
        $taskData['assigned_to'] = $user->id;
        $taskData['created_by'] = $user->id;
        $task = Task::factory()->create($taskData);

        $request = $this->request;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->getRealURL($this->search, [$task->id]), $request);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => $request['title'],
                    'status' => $request['status'],
                ],
            ]);

        $this->assertDatabaseMissing('tasks', $taskData);
        $this->assertDatabaseHas('tasks', $request);
    }

    /**
     * Test for user can send an empty request to update.
     */
    public function testUserCanSendAnEmptyRequestToUpdate(): void
    {
        list($token, $userId) = $this->login();

        $projectData['created_by'] = $userId;
        $project = Project::factory()->create($projectData);

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $userId,
            'role' => 'owner',
            'created_by' => $userId,
        ]);

        $taskData = $this->taskData;
        $taskData['project_id'] = $project->id;
        $taskData['assigned_to'] = $userId;
        $taskData['created_by'] = $userId;
        $task = Task::factory()->create($taskData);

        $request = [];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->getRealURL($this->search, [$task->id]), $request);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => $taskData['title'],
                    'status' => $taskData['status'],
                ],
            ]);

        $this->assertDatabaseHas('tasks', $taskData);
    }

    /**
     * Test for user cannot send an empty data request to update.
     */
    public function testUserCannotUpdateAResourceWithEmptyData(): void
    {
        list($token, $userId) = $this->login();

        $projectData['created_by'] = $userId;
        $project = Project::factory()->create($projectData);

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $userId,
            'role' => 'owner',
            'created_by' => $userId,
        ]);

        $taskData = $this->taskData;
        $taskData['project_id'] = $project->id;
        $taskData['assigned_to'] = $userId;
        $taskData['created_by'] = $userId;
        $task = Task::factory()->create($taskData);

        $request = [
            'title' => '',
            'status' => '',
            'assigned_to' => '',
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->getRealURL($this->search, [$task->id]), $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['title' => ['The title field is required.']],
                ['status' => ['The status field is required.']],
                ['assigned_to' => ['The assigned_to field is required.']],
            ]);
    }
}
