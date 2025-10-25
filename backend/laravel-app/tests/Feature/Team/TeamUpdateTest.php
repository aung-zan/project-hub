<?php

namespace Tests\Feature\Team;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\TestCase;

class TeamUpdateTest extends TestCase
{
    use RefreshDatabase;
    use TestHelper;

    private string $teamURL = 'http://localhost/api/teams/';
    private array $teamData = [
        'name' => 'testing Team',
        'description' => 'Team for testing.',
    ];
    private array $request = [
        'name' => 'Testing Team',
        'description' => 'A team for testing.',
    ];

    /**
     * Test for user cannot access update without jwt token.
     */
    public function testUserCannotAccessUpdateWithoutToken(): void
    {
        $response = $this->putJson($this->teamURL . '1', []);

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
    public function testUserCannotAccessUpdateWithWrongToken(): void
    {
        $token = 'abc';

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->teamURL . '1', []);

        $response->assertStatus(401)
            ->assertJsonFragments([
                ['success' => false,],
                ['error' => 'INVALID_TOKEN'],
                ['message' => 'Token is malformed or invalid.'],
            ]);
    }

    /**
     * Test for user cannot access update with wrong team id.
     */
    public function testUserCannotAccessUpdateWithWrongTeamId(): void
    {
        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->teamURL . '1', []);

        $response->assertStatus(404)
            ->assertJsonFragments([
                ['success' => false,],
                ['message' => 'Resource not found.'],
            ]);
    }

    /**
     * Test for user cannot access update with unauthorized team id.
     */
    public function testUserCannotAccessUpdateWithUnauthorizedTeamId(): void
    {
        $user = User::factory()->create();

        $teamData = $this->teamData;
        $teamData['created_by'] = $user->id;

        $team = Team::factory()->create($teamData);

        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->teamURL . $team->id, []);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Resource not found.'
            ]);
    }

    /**
     * Test for user can send an empty request to update.
     */
    public function testUserCanSendEmptyRequestToUpdate(): void
    {
        list($token, $id) = $this->login();

        $teamData = $this->teamData;
        $teamData['created_by'] = $id;

        $team = Team::factory()->create($teamData);

        $request = [];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->teamURL . $team->id, $request);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $teamData['name'],
                    'description' => $teamData['description'],
                    'created_by' => $teamData['created_by'],
                ]
            ]);
    }

    /**
     * Test for user cannot send empty request to update.
     */
    public function testUserCannotSendEmptyValueRequestToUpdate(): void
    {
        list($token, $id) = $this->login();

        $teamData = $this->teamData;
        $teamData['created_by'] = $id;

        $team = Team::factory()->create($teamData);

        $request = [
            'name' => '',
            'description' => '',
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->teamURL . $team->id, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['name' => ['The name field is required.']],
            ]);
    }

    /**
     * Test for user can send name only request to update.
     */
    public function testUserCanSendNameOnlyRequestToUpdate(): void
    {
        list($token, $id) = $this->login();

        $teamData = $this->teamData;
        $teamData['created_by'] = $id;

        $team = Team::factory()->create($teamData);

        $request['name'] = $this->request['name'];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->teamURL . $team->id, $request);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $request['name'],
                    'description' => $teamData['description'],
                    'created_by' => $teamData['created_by'],
                ]
            ]);

        $this->assertDatabaseHas('teams', [
            'name' => $request['name'],
            'description' => $teamData['description'],
            'created_by' => $teamData['created_by'],
        ]);
    }
}
