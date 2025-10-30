<?php

namespace Tests\Feature\Project;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\TestCase;

class ProjectStoreTest extends TestCase
{
    use RefreshDatabase;
    use TestHelper;

    private string $projectURL = 'http://localhost/api/projects';
    private array $projectData = [
        'name' => 'another testing',
        'status' => 'active',
    ];

    /**
     * Test for user cannot access store without jwt token.
     */
    public function testUserCannotAccessStoreWithoutToken(): void
    {
        $response = $this->postJson($this->projectURL, []);

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
    public function testUserCannotAccessStoreWithWrongToken(): void
    {
        $token = 'abc';

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson($this->projectURL, []);

        $response->assertStatus(401)
            ->assertJsonFragments([
                ['success' => false,],
                ['error' => 'INVALID_TOKEN'],
                ['message' => 'Token is malformed or invalid.'],
            ]);
    }

    /**
     * Test for user cannot create resource with empty data.
     */
    public function testUserCannotCreateWithEmptyData(): void
    {
        $request = [];

        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson($this->projectURL, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
            ]);
    }

    /**
     * Test for user cannot create resource without name field.
     */
    public function testUserCannotCreateWithoutNameAndStatus(): void
    {
        list($token) = $this->login();

        $request = [
            'description' => 'hello world',
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson($this->projectURL, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['name' => ['The name field is required.']],
                ['status' => ['The status field is required.']],
            ]);
    }

    /**
     * Test for user cannot create with wrong date format.
     */
    public function testUserCannotCreateWithWrongDateFormat(): void
    {
        list($token) = $this->login();

        $request = $this->projectData;
        $request['start_date'] = 'Mon 27 Oct';

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson($this->projectURL, $request);

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
     * Test for user cannot create with start date greater than end date.
     */
    public function testUserCannotCreateWithStartDateGreaterThanEndDate(): void
    {
        list($token) = $this->login();

        $request = $this->projectData;
        $request['start_date'] = '2025-10-28';
        $request['end_date'] = '2025-10-27';

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson($this->projectURL, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['end_date' => ['The end date field must be a date after start date.']],
            ]);
    }

    /**
     * Test for user can create with right data.
     */
    public function testUserCanCreateWithRightData(): void
    {
        list($token) = $this->login();

        $request = $this->projectData;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson($this->projectURL, $request);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $request['name'],
                    'status' => $request['status']
                ]
            ]);

        $this->assertDatabaseHas('projects', $request);
    }
}
