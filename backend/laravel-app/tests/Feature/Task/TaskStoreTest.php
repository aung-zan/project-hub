<?php

namespace Tests\Feature\Task;

use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\FeatureTestCase;

class TaskStoreTest extends FeatureTestCase
{
    use RefreshDatabase;
    use TestHelper;

    protected string $method = 'post';
    protected string $url = 'http://localhost/api/projects/{projectId}/tasks';

    private array $search = ['{projectId}'];
    private array $requestData = [
        'title' => 'Testing',
    ];

    /**
     * Test for user cannot create a resource with empty data.
     */
    public function testUserCannotCreateAResourceWithEmptyData(): void
    {
        list($token, $userId) = $this->login();

        $project = Project::factory()->create(['created_by' => $userId]);

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $userId,
            'role' => 'owner',
            'created_by' => $userId,
        ]);

        $request = [];
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                $this->getRealURL($this->search, [$project->id]),
                $request
            );

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['title' => ['The title field is required.']],
            ]);
    }

    /**
     * Test for user cannot create a resource with wrong status and worng priority.
     */
    public function testUserCannotCreateAResourceWithWrongStatusAndWrongPriority(): void
    {
        list($token, $userId) = $this->login();

        $project = Project::factory()->create(['created_by' => $userId]);

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $userId,
            'role' => 'owner',
            'created_by' => $userId,
        ]);

        $request = $this->requestData;
        $request['status'] = 'active';
        $request['priority'] = 'top';

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                $this->getRealURL($this->search, [$project->id]),
                $request
            );

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['status' => ['The selected status is invalid.']],
                ['priority' => ['The selected priority is invalid.']],
            ]);
    }

    /**
     * Test for user cannot create a resource with assigned to non-exist user id.
     */
    public function testUserCannotCreateAResourceWithAssignedToNonExistUserId(): void
    {
        list($token, $userId) = $this->login();

        $project = Project::factory()->create(['created_by' => $userId]);

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $userId,
            'role' => 'owner',
            'created_by' => $userId,
        ]);

        $request = $this->requestData;
        $request['assigned_to'] = 10;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                $this->getRealURL($this->search, [$project->id]),
                $request
            );

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['assigned_to' => ['The selected assigned_to is invalid.']],
            ]);
    }

    /**
     * Test for user cannot create a resource with wrong date format.
     */
    public function testUserCannotCreateAResourceWithWrongDateFormat(): void
    {
        list($token, $userId) = $this->login();

        $project = Project::factory()->create(['created_by' => $userId]);

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $userId,
            'role' => 'owner',
            'created_by' => $userId,
        ]);

        $request = $this->requestData;
        $request['due_date'] = 'Mon 27 Oct';

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                $this->getRealURL($this->search, [$project->id]),
                $request
            );

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['due_date' => [
                    'The due_date field must be a valid date.',
                    'The due_date field must match the format Y-m-d.',
                ]],
            ]);
    }

    /**
     * Test for user cannot create a resource without role.
     */
    public function testUserCannotCreateAResourceWithoutRole(): void
    {
        $user = User::factory()->create();

        $project = Project::factory()->create(['created_by' => $user->id]);

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'role' => 'owner',
            'created_by' => $user->id,
        ]);

        $request = $this->requestData;

        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                $this->getRealURL($this->search, [$project->id]),
                $request
            );

        $response->assertStatus(401)
            ->assertJsonFragments([
                ['success' => false],
                ['message' => 'This action is unauthorized.'],
            ]);
    }

    /**
     * Test for use cannot create a resource with viewer role.
     */
    public function testUserCannotCreateAResourceWithViewerRole(): void
    {
        $user = User::factory()->create();

        $project = Project::factory()->create(['created_by' => $user->id]);

        list($token, $userId) = $this->login();

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $userId,
            'role' => 'viewer',
            'created_by' => $user->id,
        ]);

        $request = $this->requestData;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                $this->getRealURL($this->search, [$project->id]),
                $request
            );

        $response->assertStatus(401)
            ->assertJsonFragments([
                ['success' => false],
                ['message' => 'This action is unauthorized.'],
            ]);
    }

    /**
     * Test for user can create a resource with right data.
     */
    public function testUserCanCreateAResourceWithRightData(): void
    {
        $user = User::factory()->create();

        $project = Project::factory()->create(['created_by' => $user->id]);

        list($token, $userId) = $this->login();

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $userId,
            'role' => 'member',
            'created_by' => $user->id,
        ]);

        $request = $this->requestData;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                $this->getRealURL($this->search, [$project->id]),
                $request
            );

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'title' => $request['title'],
                ],
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => $request['title'],
            'status' => 'todo',
            'priority' => 'low',
        ]);
    }
}
