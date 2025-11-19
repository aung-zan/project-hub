<?php

namespace Tests\Feature\ProjectUser;

use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\FeatureTestCase;

class ProjectUserStoreTest extends FeatureTestCase
{
    use RefreshDatabase;
    use TestHelper;

    protected string $method = 'post';
    protected string $url = 'http://localhost/api/projects/{id}/members';
    private array $projectData = [
        'name' => 'another project'
    ];

    /**
     * Create a request array for project_user.
     *
     * @param array $roles
     * @param array $memberIds
     * @return array
     */
    private function createRequest(array $roles, array $memberIds): array
    {
        $request['members'] = [];
        $role = '';

        foreach ($memberIds as $key => $memberId) {
            $role = $roles[$key] ?? $role;
            $request['members'][] = [
                'id' => $memberId,
                'role' => $role
            ];
        }

        return $request;
    }

    /**
     * Test for user cannot create a resource with empty data.
     */
    public function testUserCannotCreateAResourceWithEmptyData(): void
    {
        $request = [];
        $projectData = $this->projectData;

        list($token, $id) = $this->login();

        $projectData['created_by'] = $id;
        $project = Project::factory()->create($projectData);

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $id,
            'role' => 'owner',
            'created_by' => $id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                $this->getRealURL(['{id}'], [$project->id]),
                $request
            );

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['members' => ['The members field is required.']],
            ]);
    }

    /**
     * Test for user cannot create a resource with fake user_id and unknown role.
     */
    public function testUserCannotCreateAResourceWithFakeMemberIdAndUnknownRole(): void
    {
        $projectData = $this->projectData;

        list($token, $id) = $this->login();

        $projectData['created_by'] = $id;
        $project = Project::factory()->create($projectData);

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $id,
            'role' => 'owner',
            'created_by' => $id,
        ]);

        $request = $this->createRequest(['test'], [10, 11]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                $this->getRealURL(['{id}'], [$project->id]),
                $request
            );

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['members.0.id' => ['Resource ID:10 not found.']],
                ['members.0.role' => ['The selected member role is invalid.']],
                ['members.1.id' => ['Resource ID:11 not found.']],
                ['members.1.role' => ['The selected member role is invalid.']],
            ]);
    }

    /**
     * Test for user cannot create a resource with member id.
     */
    public function testUserCannotCreateAResourceWithMemberIdAndRightRole(): void
    {
        $projectData = $this->projectData;

        list($token, $id) = $this->login();

        $projectData['created_by'] = $id;
        $project = Project::factory()->create($projectData);

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $id,
            'role' => 'owner',
            'created_by' => $id,
        ]);

        $request = $this->createRequest(['member'], [$id]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                $this->getRealURL(['{id}'], [$project->id]),
                $request
            );

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['members.0.id' => ["ID:$id is already member in the project."]],
            ]);
    }

    /**
     * Test for user can create a resource with right id and right role.
     */
    public function testUserCanCreateAResourceWithRightIdAndRightRole(): void
    {
        $projectData = $this->projectData;

        list($token, $id) = $this->login();

        $projectData['created_by'] = $id;
        $project = Project::factory()->create($projectData);

        ProjectUser::factory()->create([
            'project_id' => $project->id,
            'user_id' => $id,
            'role' => 'owner',
            'created_by' => $id,
        ]);

        $user = User::factory()->create();

        $request = $this->createRequest(['member'], [$user->id]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                $this->getRealURL(['{id}'], [$project->id]),
                $request
            );

        $response->assertStatus(200)
            ->assertJsonFragments([
                ['success' => true],
                ['name' => $user->name],
                ['username' => $user->username],
                ['email' => $user->email],
            ]);
    }
}
