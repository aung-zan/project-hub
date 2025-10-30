<?php

namespace Tests\Feature\ProjectUser;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\TestCase;

class ProjectUserDestroyTest extends TestCase
{
    use RefreshDatabase;
    use TestHelper;

    private string $projectUserURL = 'http://localhost/api/projects/{projectId}/members/{memberId}';
    private array $projectData = [
        'name' => 'another project'
    ];

    /**
     * Return the real url.
     *
     * @param int $teamId
     * @param int $memberId
     * @return string
     */
    private function getRealURL(int $projectId, int $memberId): string
    {
        return str_replace(['{projectId}', '{memberId}'], [$projectId, $memberId], $this->projectUserURL);
    }

    /**
     * Test for user cannot access destroy without jwt token.
     */
    public function testUserCannotAccessDestroyWithoutToken(): void
    {
        $response = $this->deleteJson($this->getRealURL(1, 1));

        $response->assertStatus(401)
            ->assertJsonFragments([
                ['success' => false,],
                ['error' => 'TOKEN_NOT_PROVIDED'],
                ['message' => 'Token is not provided in header.'],
            ]);
    }

    /**
     * Test for user cannot access destroy with wrong jwt token.
     */
    public function testUserCannotAccessDestroyWithWrongToken(): void
    {
        $token = 'abc';

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->getRealURL(1, 1));

        $response->assertStatus(401)
            ->assertJsonFragments([
                ['success' => false,],
                ['error' => 'INVALID_TOKEN'],
                ['message' => 'Token is malformed or invalid.'],
            ]);
    }

    /**
     * Test for user cannot send fake project and member id to destory.
     */
    public function testUserCannotSendFakeProjectAndMemberIdToDestroy(): void
    {
        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->getRealURL(1, 1));

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Resource not found.'
            ]);
    }

    /**
     * Test for user cannot send a request with unauthorized project id to destroy.
     */
    public function testUserCannotSendARequestWithUnauthorizedProjectIdToDestroy(): void
    {
        $user = User::factory()->create();

        $projectData = $this->projectData;
        $projectData['created_by'] = $user->id;

        $project = Project::factory()->create($projectData);
        $project->users()->attach([$user->id => ['created_by' => $user->id]]);

        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->getRealURL($project->id, $user->id));

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Resource not found.',
            ]);
    }

    /**
     * Test for user can send a request with right project and member id to destory.
     */
    public function testUserCanSendARequestWithRightProjectAndMemberIdToDestroy(): void
    {
        list($token, $id) = $this->login();

        $projectData = $this->projectData;
        $projectData['created_by'] = $id;

        $project = Project::factory()->create($projectData);
        $project->users()->attach([$id => ['created_by' => $id]]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->getRealURL($project->id, $id));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Successfully remove a member.',
            ]);
    }
}
