<?php

namespace Tests\Feature\Team;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\TestCase;

class TeamDestroyTest extends TestCase
{
    use RefreshDatabase;
    use TestHelper;

    private string $teamURL = 'http://localhost/api/teams/';
    private array $teamData = [
        'name' => 'Testing Team',
        'description' => 'Team for testing.',
    ];

    /**
     * Test for user cannot access delete without jwt token.
     */
    public function testUserCannotAccessDeleteWithoutToken(): void
    {
        $response = $this->deleteJson($this->teamURL . '1');

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
            ->deleteJson($this->teamURL . '1');

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
            ->deleteJson($this->teamURL . '1');

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

        $teamData = $this->teamData;
        $teamData['created_by'] = $user->id;

        $team = Team::factory()->create($teamData);

        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->teamURL . $team->id);

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

        $teamData = $this->teamData;
        $teamData['created_by'] = $id;

        $team = Team::factory()->create($teamData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->teamURL . $team->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $teamData['name'],
                    'description' => $teamData['description'],
                ]
            ]);

        $this->assertDatabaseMissing('teams', $teamData);
    }
}
