<?php

namespace Tests\Feature\Project;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\FeatureTestCase;

class ProjectStoreTest extends FeatureTestCase
{
    use RefreshDatabase;
    use TestHelper;

    protected string $method = 'post';
    protected string $url = 'http://localhost/api/projects';
    private array $projectData = [
        'name' => 'another testing',
        'status' => 'active',
    ];

    /**
     * Test for user cannot create a resource with empty data.
     */
    public function testUserCannotCreateAResourceWithEmptyData(): void
    {
        $request = [];

        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson($this->url, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
            ]);
    }

    /**
     * Test for user cannot create a resource without name field.
     */
    public function testUserCannotCreateAResourceWithoutNameAndStatus(): void
    {
        list($token) = $this->login();

        $request = [
            'description' => 'hello world',
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson($this->url, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['name' => ['The name field is required.']],
                ['status' => ['The status field is required.']],
            ]);
    }

    /**
     * Test for user cannot create a resource with wrong date format.
     */
    public function testUserCannotCreateAResourceWithWrongDateFormat(): void
    {
        list($token) = $this->login();

        $request = $this->projectData;
        $request['start_date'] = 'Mon 27 Oct';

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson($this->url, $request);

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
     * Test for user cannot create a resource with start date greater than end date.
     */
    public function testUserCannotCreateAResourceWithStartDateGreaterThanEndDate(): void
    {
        list($token) = $this->login();

        $request = $this->projectData;
        $request['start_date'] = '2025-10-28';
        $request['end_date'] = '2025-10-27';

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson($this->url, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['end_date' => ['The end date field must be a date after start date.']],
            ]);
    }

    /**
     * Test for user can create a resource with right data.
     */
    public function testUserCanCreateAResourceWithRightData(): void
    {
        list($token) = $this->login();

        $request = $this->projectData;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson($this->url, $request);

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
