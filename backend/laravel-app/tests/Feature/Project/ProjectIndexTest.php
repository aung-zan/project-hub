<?php

namespace Tests\Feature\Project;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\TestCase;

class ProjectIndexTest extends TestCase
{
    use RefreshDatabase;
    use TestHelper;

    private string $projectURL = 'http://localhost/api/projects';
    private array $projectData = [
        'name' => 'another testing',
        'status' => 'active',
    ];

    /**
     * Test for user cannot access the data without jwt token.
     */
    public function testUserCannotAccessIndexWithoutToken(): void
    {
        $response = $this->getJson($this->projectURL);

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
    public function testUserCannotAccessIndexWithWrongToken(): void
    {
        $token = 'abc';

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson($this->projectURL);

        $response->assertStatus(401)
            ->assertJsonFragments([
                ['success' => false,],
                ['error' => 'INVALID_TOKEN'],
                ['message' => 'Token is malformed or invalid.'],
            ]);
    }

    /**
     * Test for user access the data with right token.
     */
    public function testUserCanAccessIndexWithRightToken(): void
    {
        list($token, $id) = $this->login();

        $projectData = $this->projectData;
        $projectData['created_by'] = $id;

        Project::factory()->create($projectData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson($this->projectURL);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    [
                        'name' => $projectData['name'],
                        'status' => $projectData['status'],
                    ]
                ]
            ]);
    }
}
