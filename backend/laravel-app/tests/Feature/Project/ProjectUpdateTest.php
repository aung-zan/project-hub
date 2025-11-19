<?php

namespace Tests\Feature\Project;

use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\FeatureTestCase;

class ProjectUpdateTest extends FeatureTestCase
{
    use RefreshDatabase;
    use TestHelper;

    protected string $method = 'put';
    protected string $url = 'http://localhost/api/projects/{id}';
    private array $projectData = [
        'name' => 'testing',
        'status' => 'active',
    ];
    private array $request = [
        'name' => 'another testing',
        'status' => 'completed',
    ];
    private array $search = ['{id}'];

    /**
     * Test for user cannot update a resource with wrong id.
     */
    public function testUserCannotUpdateAResourceWithWrongId(): void
    {
        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->getRealURL($this->search, [1]), []);

        $response->assertStatus(404)
            ->assertJsonFragments([
                ['success' => false,],
                ['message' => 'Resource not found.'],
            ]);
    }

    /**
     * Test for user cannot update a resource with unauthorized id.
     */
    public function testUserCannotUpdateAResourceWithUnauthorizedId(): void
    {
        list($token, $id) = $this->login();

        $projectData = $this->projectData;
        $projectData['created_by'] = $id;

        $project = Project::factory()->create($projectData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->getRealURL($this->search, [$project->id]), []);

        $response->assertStatus(404)
            ->assertJsonFragments([
                ['success' => false,],
                ['message' => 'Resource not found.'],
            ]);
    }

    /**
     * Test for user can only send an empty request to update.
     * The data is not updated or changed.
     */
    public function testUserCanSendAnEmptyRequestToUpdate(): void
    {
        list($token, $id) = $this->login();

        $projectData = $this->projectData;
        $projectData['created_by'] = $id;

        $project = Project::factory()->create($projectData);

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $id,
            'role' => 'owner',
            'created_by' => $id,
        ]);

        $request = [];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->getRealURL($this->search, [$project->id]), $request);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $projectData['name'],
                    'status' => $projectData['status'],
                    'created_by' => $projectData['created_by'],
                ]
            ]);

        $this->assertDatabaseHas('projects', $projectData);
    }

    /**
     * Test for user cannot update a resource with empty data.
     */
    public function testUserCannotUpdateAResourceWithEmptyData(): void
    {
        list($token, $id) = $this->login();

        $projectData = $this->projectData;
        $projectData['created_by'] = $id;

        $project = Project::factory()->create($projectData);

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $id,
            'role' => 'owner',
            'created_by' => $id,
        ]);

        $request = [
            'name' => '',
            'status' => '',
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->getRealURL($this->search, [$project->id]), $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['name' => ['The name field is required.']],
                ['status' => ['The status field is required.']],
            ]);
    }

    /**
     * Test for user cannot update a resource with wrong date format.
     */
    public function testUserCannotUpdateAResourceWithWrongDateFormat(): void
    {
        list($token, $id) = $this->login();

        $projectData = $this->projectData;
        $projectData['created_by'] = $id;

        $project = Project::factory()->create($projectData);

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $id,
            'role' => 'owner',
            'created_by' => $id,
        ]);

        $request = $this->request;
        $request['start_date'] = 'Mon 27 Oct';

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->getRealURL($this->search, [$project->id]), $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['start_date' => [
                    'The start date field must be a valid date.',
                    'The start date field must match the format Y-m-d.',
                ]],
            ]);
    }

    /**
     * Test for user cannot update a resource with start date greater than end date.
     */
    public function testUserCannotUpdateAResourceWithStartDateGreaterThanEndDate(): void
    {
        list($token, $id) = $this->login();

        $projectData = $this->projectData;
        $projectData['created_by'] = $id;

        $project = Project::factory()->create($projectData);

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $id,
            'role' => 'owner',
            'created_by' => $id,
        ]);

        $request = $this->request;
        $request['start_date'] = '2025-10-28';
        $request['end_date'] = '2025-10-27';

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->getRealURL($this->search, [$project->id]), $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['end_date' => ['The end date field must be a date after start date.']],
            ]);
    }

    /**
     * Test for user can update a resource with right data.
     */
    public function testUserCanUpdateAResourceWithRightData(): void
    {
        list($token, $id) = $this->login();

        $projectData = $this->projectData;
        $projectData['created_by'] = $id;

        $project = Project::factory()->create($projectData);

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $id,
            'role' => 'owner',
            'created_by' => $id,
        ]);

        $request = $this->request;
        $request['start_date'] = '2025-10-27';
        $request['end_date'] = '2025-10-30';

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->getRealURL($this->search, [$project->id]), $request);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $request['name'],
                    'status' => $request['status'],
                    'start_date' => $request['start_date'],
                    'end_date' => $request['end_date'],
                ],
            ]);

        $this->assertDatabaseHas('projects', $request);
    }
}
