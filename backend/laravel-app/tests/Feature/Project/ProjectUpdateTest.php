<?php

namespace Tests\Feature\Project;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\TestCase;

class ProjectUpdateTest extends TestCase
{
    use RefreshDatabase;
    use TestHelper;

    private string $projectURL = 'http://localhost/api/projects/';
    private array $projectData = [
        'name' => 'testing',
        'status' => 'active',
    ];
    private array $request = [
        'name' => 'another testing',
        'status' => 'completed',
    ];

    /**
     * Test for user cannot access update without jwt token.
     */
    public function testUserCannotAccessUpdateWithoutToken(): void
    {
        $response = $this->putJson($this->projectURL . '1', []);

        $response->assertStatus(401)
            ->assertJsonFragments([
                ['success' => false,],
                ['error' => 'TOKEN_NOT_PROVIDED'],
                ['message' => 'Token is not provided in header.'],
            ]);
    }

    /**
     * Test for user cannot access the data with wrong jwt token.
     */
    public function testUserCannotAccessUpdateWithWrongToken(): void
    {
        $token = 'abc';

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->projectURL . '1', []);

        $response->assertStatus(401)
            ->assertJsonFragments([
                ['success' => false,],
                ['error' => 'INVALID_TOKEN'],
                ['message' => 'Token is malformed or invalid.'],
            ]);
    }

    /**
     * Test for user cannot access update with wrong id.
     */
    public function testUserCannotAccessUpdateWithWrongId(): void
    {
        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->projectURL . '1', []);

        $response->assertStatus(404)
            ->assertJsonFragments([
                ['success' => false,],
                ['message' => 'Resource not found.'],
            ]);
    }

    /**
     * Test for user cannot access update with unauthorized id.
     */
    public function testUserCannotAccessUpdateWithUnauthorizedId(): void
    {
        $user = User::factory()->create();

        $projectData = $this->projectData;
        $projectData['created_by'] = $user->id;

        $project = Project::factory()->create($projectData);

        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->projectURL . $project->id, []);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Resource not found.'
            ]);
    }

    /**
     * Test for user can send an empty request to update.
     */
    public function testUserCanSendEmptyRequestToUpdate(): void
    {
        list($token, $id) = $this->login();

        $projectData = $this->projectData;
        $projectData['created_by'] = $id;

        $project = Project::factory()->create($projectData);

        $request = [];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->projectURL . $project->id, $request);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $projectData['name'],
                    'status' => $projectData['status'],
                    'created_by' => $projectData['created_by'],
                ]
            ]);
    }

    /**
     * Test for user cannot send empty request to update.
     */
    public function testUserCannotSendEmptyValueRequestToUpdate(): void
    {
        list($token, $id) = $this->login();

        $projectData = $this->projectData;
        $projectData['created_by'] = $id;

        $project = Project::factory()->create($projectData);

        $request = [
            'name' => '',
            'status' => '',
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->projectURL . $project->id, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['name' => ['The name field is required.']],
                ['status' => ['The status field is required.']],
            ]);
    }

    /**
     * Test for user cannot update with wrong date format.
     */
    public function testUserCannotUpdateWithWrongDateFormat(): void
    {
        list($token, $id) = $this->login();

        $projectData = $this->projectData;
        $projectData['created_by'] = $id;

        $project = Project::factory()->create($projectData);

        $request = $this->request;
        $request['start_date'] = 'Mon 27 Oct';

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->projectURL . $project->id, $request);

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
     * Test for user cannot update with start date greater than end date.
     */
    public function testUserCannotUpdateWithStartDateGreaterThanEndDate(): void
    {
        list($token, $id) = $this->login();

        $projectData = $this->projectData;
        $projectData['created_by'] = $id;

        $project = Project::factory()->create($projectData);

        $request = $this->request;
        $request['start_date'] = '2025-10-28';
        $request['end_date'] = '2025-10-27';

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->projectURL . $project->id, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['end_date' => ['The end date field must be a date after start date.']],
            ]);
    }

    /**
     * Test for user can update with right data.
     */
    public function testUserCanUpdateWithRightData(): void
    {
        list($token, $id) = $this->login();

        $projectData = $this->projectData;
        $projectData['created_by'] = $id;

        $project = Project::factory()->create($projectData);

        $request = $this->request;
        $request['start_date'] = '2025-10-27';
        $request['end_date'] = '2025-10-30';

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->projectURL . $project->id, $request);

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
