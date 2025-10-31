<?php

namespace Tests\Feature\Project;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\FeatureTestCase;
use Tests\TestCase;

class ProjectDestroyTest extends FeatureTestCase
{
    use RefreshDatabase;
    use TestHelper;

    protected string $url = 'http://localhost/api/projects/';
    private array $projectData = [
        'name' => 'testing',
        'status' => 'active',
    ];

    /**
     * Test for user cannot delete a resource with wrong project id.
     */
    public function testUserCannotDeleteAResourceWithWrongId(): void
    {
        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->url . '1');

        $response->assertStatus(404)
            ->assertJsonFragments([
                ['success' => false,],
                ['message' => 'Resource not found.'],
            ]);
    }

    /**
     * Test for user cannot delete a resource with unauthorized project id.
     */
    public function testUserCannotDeleteAResourceWithUnauthorizedId(): void
    {
        $user = User::factory()->create();

        $projectData = $this->projectData;
        $projectData['created_by'] = $user->id;

        $project = Project::factory()->create($projectData);

        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->url . $project->id);

        $response->assertStatus(404)
            ->assertJsonFragments([
                ['success' => false,],
                ['message' => 'Resource not found.'],
            ]);
    }

    /**
     * Test for user can delete a resource with right project id.
     */
    public function testUserCanDeleteAResourceWithRightId(): void
    {
        list($token, $id) = $this->login();

        $projectData = $this->projectData;
        $projectData['created_by'] = $id;

        $project = Project::factory()->create($projectData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->url . $project->id);

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
