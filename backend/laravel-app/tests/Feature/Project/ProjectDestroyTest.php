<?php

namespace Tests\Feature\Project;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\TestCase;

class ProjectDestroyTest extends TestCase
{
    use RefreshDatabase;
    use TestHelper;

    private string $projectURL = 'http://localhost/api/projects/';
    private array $projectData = [
        'name' => 'testing',
        'status' => 'active',
    ];

    /**
     * Test for user cannot access delete without jwt token.
     */
    public function testUserCannotAccessDeleteWithoutToken(): void
    {
        $response = $this->deleteJson($this->projectURL . '1');

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
    public function testUserCannotAccessDeleteWithWrongToken(): void
    {
        $token = 'abc';

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->projectURL . '1');

        $response->assertStatus(401)
            ->assertJsonFragments([
                ['success' => false,],
                ['error' => 'INVALID_TOKEN'],
                ['message' => 'Token is malformed or invalid.'],
            ]);
    }

    /**
     * Test for user cannot access delete with wrong team id.
     */
    public function testUserCannotAccessDeleteWithWrongTeamId(): void
    {
        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->projectURL . '1');

        $response->assertStatus(404)
            ->assertJsonFragments([
                ['success' => false,],
                ['message' => 'Resource not found.'],
            ]);
    }

    /**
     * Test for user cannot access delete with unauthorized team id.
     */
    public function testUserCannotAccessDeleteWithUnauthorizedTeamId(): void
    {
        $user = User::factory()->create();

        $projectData = $this->projectData;
        $projectData['created_by'] = $user->id;

        $project = Project::factory()->create($projectData);

        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->projectURL . $project->id);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Resource not found.'
            ]);
    }

    /**
     * Test for user can access delete with right team id.
     */
    public function testUserCanAcessDeleteWithRightTeamId(): void
    {
        list($token, $id) = $this->login();

        $projectData = $this->projectData;
        $projectData['created_by'] = $id;

        $project = Project::factory()->create($projectData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->projectURL . $project->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $projectData['name'],
                    'status' => $projectData['status'],
                ]
            ]);

        $this->assertDatabaseMissing('projects', $projectData);
    }
}
