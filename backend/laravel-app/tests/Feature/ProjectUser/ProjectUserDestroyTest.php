<?php

namespace Tests\Feature\ProjectUser;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\FeatureTestCase;

class ProjectUserDestroyTest extends FeatureTestCase
{
    use RefreshDatabase;
    use TestHelper;

    protected string $method = 'delete';
    protected string $url = 'http://localhost/api/projects/{projectId}/members/{memberId}';
    private array $projectData = [
        'name' => 'another project'
    ];
    private array $search = ['{projectId}', '{memberId}'];

    /**
     * Test for user cannot delete a resource with fake project and member id.
     */
    public function testUserCannotDeleteAResourceWithFakeProjectAndMemberId(): void
    {
        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->getRealURL($this->search, [1, 1]));

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Resource not found.'
            ]);
    }

    /**
     * Test for user cannot delete a resource with unauthorized project id.
     */
    public function testUserCannotDeleteAResourceWithUnauthorizedProjectId(): void
    {
        $user = User::factory()->create();

        $projectData = $this->projectData;
        $projectData['created_by'] = $user->id;

        $project = Project::factory()->create($projectData);
        $project->users()->attach([$user->id => ['created_by' => $user->id]]);

        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->getRealURL($this->search, [$project->id, 1]));

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Resource not found.',
            ]);
    }

    /**
     * Test for user can delete a resource with right project and user id.
     */
    public function testUserCanDeleteAResourceWithRightProjectAndUserId(): void
    {
        list($token, $id) = $this->login();

        $projectData = $this->projectData;
        $projectData['created_by'] = $id;

        $project = Project::factory()->create($projectData);
        $project->users()->attach([$id => ['created_by' => $id]]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->getRealURL($this->search, [$project->id, $id]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Successfully remove a member.',
            ]);
    }
}
