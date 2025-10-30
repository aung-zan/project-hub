<?php

namespace Tests\Feature\Project;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\TestCase;

class ProjectShowTest extends TestCase
{
    use RefreshDatabase;
    use TestHelper;

    private string $projectURL = 'http://localhost/api/projects/';
    private array $projectData = [
        'name' => 'another testing',
        'status' => 'active',
    ];

    /**
     * Test for user cannot access show without jwt token.
     */
    public function testUserCannotAccessShowWithoutToken(): void
    {
        $response = $this->getJson($this->projectURL . '1');

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
    public function testUserCannotAccessShowWithWrongToken(): void
    {
        $token = 'abc';

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson($this->projectURL . '1');

        $response->assertStatus(401)
            ->assertJsonFragments([
                ['success' => false,],
                ['error' => 'INVALID_TOKEN'],
                ['message' => 'Token is malformed or invalid.'],
            ]);
    }

    /**
     * Test for user cannot access show with wrong id.
     */
    public function testUserCannotAccessShowWithWrongId(): void
    {
        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson($this->projectURL . '1');

        $response->assertStatus(404)
            ->assertJsonFragments([
                ['success' => false,],
                ['message' => 'Resource not found.'],
            ]);
    }

    /**
     * Test for user cannot access show with unauthorized id.
     */
    public function testUserCannotAccessShowWithUnauthorizedId(): void
    {
        $user = User::factory()->create();

        $projectData = $this->projectData;
        $projectData['created_by'] = $user->id;

        $project = Project::factory()->create($projectData);

        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson($this->projectURL . $project->id);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Resource not found.'
            ]);
    }

    /**
     * Test for user can access show with right id.
     */
    public function testUserCanAcessShowWithRightId(): void
    {
        list($token, $id) = $this->login();

        $projectData = $this->projectData;
        $projectData['created_by'] = $id;

        $project = Project::factory()->create($projectData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson($this->projectURL . $project->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $projectData['name'],
                    'status' => $projectData['status'],
                ]
            ]);
    }
}
