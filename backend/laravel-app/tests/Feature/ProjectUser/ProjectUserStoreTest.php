<?php

namespace Tests\Feature\ProjectUser;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\TestCase;

class ProjectUserStoreTest extends TestCase
{
    use RefreshDatabase;
    use TestHelper;

    private string $projectUserURL = 'http://localhost/api/projects/{id}/members';
    private array $projectData = [
        'name' => 'another project'
    ];

    /**
     * Test for user cannot access store without jwt token.
     */
    public function testUserCannotAccessStoreWithoutToken(): void
    {
        $response = $this->postJson($this->projectUserURL, []);

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
            ->postJson($this->projectUserURL, []);

        $response->assertStatus(401)
            ->assertJsonFragments([
                ['success' => false,],
                ['error' => 'INVALID_TOKEN'],
                ['message' => 'Token is malformed or invalid.'],
            ]);
    }

    /**
     * Test for user cannot send an empty request to store.
     */
    public function testUserCannotSendAnEmptyRequestToStore(): void
    {
        $request = [];
        $projectData = $this->projectData;

        list($token, $id) = $this->login();
        $projectData['created_by'] = $id;

        $project = Project::factory()->create($projectData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                str_replace('{id}', $project->id, $this->projectUserURL),
                $request
            );

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['member_id' => ['The member_id field is required.']],
            ]);
    }

    /**
     * Test for user cannot send a request without members' ids.
     */
    public function testUserCannotSendARequestWithoutAnArrayOfMemberId(): void
    {
        $request = ['member_id' => 1];
        $projectData = $this->projectData;

        list($token, $id) = $this->login();
        $projectData['created_by'] = $id;

        $project = Project::factory()->create($projectData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                str_replace('{id}', $project->id, $this->projectUserURL),
                $request
            );

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['member_id' => ['The member_id field must be an array.']],
            ]);
    }

    /**
     * Test for user cannot send a request with string type member's ids.
     */
    public function testUserCannotSendARequestWithStringofMemberId(): void
    {
        $request = ['member_id' => ['1', '2']];
        $projectData = $this->projectData;

        list($token, $id) = $this->login();
        $projectData['created_by'] = $id;

        $project = Project::factory()->create($projectData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                str_replace('{id}', $project->id, $this->projectUserURL),
                $request
            );

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['member_id.0' => ['The member_id field must be an integer.']],
                ['member_id.1' => ['The member_id field must be an integer.']],
            ]);
    }

    /**
     * Test for user cannot send a request with fake members' ids.
     */
    public function testUserCannotSendARequestWithFakeMemberId(): void
    {
        $request = ['member_id' => [10, 11]];
        $projectData = $this->projectData;

        list($token, $id) = $this->login();
        $projectData['created_by'] = $id;

        $project = Project::factory()->create($projectData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                str_replace('{id}', $project->id, $this->projectUserURL),
                $request
            );

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['member_id.0' => ['The selected member_id is invalid.']],
                ['member_id.1' => ['The selected member_id is invalid.']],
            ]);
    }

    /**
     * Test for user can send a request with right data.
     */
    public function testUserCanSendARequestWithExistIntegerMemberId(): void
    {
        $projectData = $this->projectData;

        list($token, $id) = $this->login();
        $projectData['created_by'] = $id;

        $project = Project::factory()->create($projectData);
        $user = User::factory()->create();

        $request = ['member_id' => [$id, $user->id]];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                str_replace('{id}', $project->id, $this->projectUserURL),
                $request
            );

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'members' => [$id, $user->id]
                ]
            ]);
    }
}
