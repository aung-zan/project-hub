<?php

namespace Tests\Feature\TeamUser;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\TestCase;

class TeamUserDestroyTest extends TestCase
{
    use RefreshDatabase;
    use TestHelper;

    private string $teamUserURL = 'http://localhost/api/teams/{teamId}/members/{memberId}';
    private array $teamData = [
        'name' => 'Testing team A'
    ];

    /**
     * Return the real url.
     *
     * @param int $teamId
     * @param int $memberId
     * @return string
     */
    private function getRealURL(int $teamId, int $memberId): string
    {
        return str_replace(['{teamId}', '{memberId}'], [$teamId, $memberId], $this->teamUserURL);
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
     * Test for user cannot send fake team and member id to destory.
     */
    public function testUserCannotSendFakeTeamAndMemberIdToDestroy(): void
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
     * Test for user cannot send fake member_id to destroy.
     */
    public function testUserCannotSendFakeMemberIdToDestroy(): void
    {
        list($token, $id) = $this->login();

        $teamData = $this->teamData;
        $teamData['created_by'] = $id;

        $team = Team::factory()->create($teamData);
        $team->users()->attach([$id => ['created_by' => $id]]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->getRealURL($team->id, 10));

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Resource not found.',
            ]);

        $this->assertDatabaseHas('team_users', [
            'team_id' => $team->id,
            'user_id' => $id,
            'created_by' => $id,
        ]);
    }

    /**
     * Test for user cannot send a request with unauthorized team id to destroy.
     */
    public function testUserCannotSendARequestWithUnauthorizedTeamIdToDestroy(): void
    {
        $user = User::factory()->create();

        $teamData = $this->teamData;
        $teamData['created_by'] = $user->id;

        $team = Team::factory()->create($teamData);

        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->getRealURL($team->id, 10));

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Resource not found.',
            ]);
    }

    /**
     * Test for user can send a request with right team and user id to destory.
     */
    public function testUserCanSendARequestWithRightTeamAndUserIdToDestroy(): void
    {
        list($token, $id) = $this->login();

        $teamData = $this->teamData;
        $teamData['created_by'] = $id;

        $team = Team::factory()->create($teamData);
        $team->users()->attach([$id => ['created_by' => $id]]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->getRealURL($team->id, $id));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Successfully remove a member.',
            ]);
    }
}
