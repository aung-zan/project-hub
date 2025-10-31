<?php

namespace Tests\Feature\TeamUser;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\FeatureTestCase;

class TeamUserDestroyTest extends FeatureTestCase
{
    use RefreshDatabase;
    use TestHelper;

    protected string $method = 'delete';
    protected string $url = 'http://localhost/api/teams/{teamId}/members/{memberId}';
    private array $teamData = [
        'name' => 'Testing team A'
    ];
    private array $search = ['{teamId}', '{memberId}'];

    /**
     * Test for user cannot a delete a resource with fake team and member id.
     */
    public function testUserCannotDeleteAResourceWithFakeTeamAndMemberId(): void
    {
        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->getRealURL($this->search, [1, 1]));

        $response->assertStatus(404)
            ->assertJsonFragments([
                ['success' => false],
                ['message' => 'Resource not found.'],
            ]);
    }

    /**
     * Test for user cannot delete a resource with unauthorized team id.
     */
    public function testUserCannotDeleteAResourceWithUnauthorizedTeamId(): void
    {
        $user = User::factory()->create();

        $teamData = $this->teamData;
        $teamData['created_by'] = $user->id;

        $team = Team::factory()->create($teamData);

        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->getRealURL($this->search, [$team->id, 1]));

        $response->assertStatus(404)
            ->assertJsonFragments([
                ['success' => false],
                ['message' => 'Resource not found.'],
            ]);
    }

    /**
     * Test for user can delete a resource with right team and user id.
     */
    public function testUserCanDeleteAResourceWithRightTeamAndUserId(): void
    {
        list($token, $id) = $this->login();

        $teamData = $this->teamData;
        $teamData['created_by'] = $id;

        $team = Team::factory()->create($teamData);
        $team->users()->attach([$id => ['created_by' => $id]]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->getRealURL($this->search, [$team->id, $id]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Successfully remove a member.',
            ]);
    }
}
