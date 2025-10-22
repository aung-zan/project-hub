<?php

namespace Tests\Feature\Team;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\TestCase;

class TeamStoreTest extends TestCase
{
    use RefreshDatabase;
    use TestHelper;

    private string $teamURL = 'http://localhost/api/teams';
    private array $request = [
        'name' => 'Testing Team',
        'description' => 'Team for testing.',
    ];

    /**
     * Test for user cannot access store without jwt token.
     */
    public function testUserCannotAccessStoreWithoutToken(): void
    {
        $response = $this->getJson($this->teamURL);

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
            ->getJson($this->teamURL);

        $response->assertStatus(401)
            ->assertJsonFragments([
                ['success' => false,],
                ['error' => 'INVALID_TOKEN'],
                ['message' => 'Token is malformed or invalid.'],
            ]);
    }

    /**
     * Test for user cannot create resource with empty data.
     */
    public function testUserCannotCreateTeamWithEmptyData(): void
    {
        $request = [];

        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson($this->teamURL, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
            ]);
    }

    /**
     * Test for user cannot create team without name field.
     */
    public function testUserCannotCreateTeamWithoutName(): void
    {
        list($token, $id) = $this->login();

        $request = [
            'description' => $this->request['description'],
            'created_by' => $id,
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson($this->teamURL, $request);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error' => 'VALIDATION_FALIED',
                'message' => [
                    'name' => ['The name field is required.']
                ],
            ]);
    }

    /**
     * Test for user can create team without description field.
     */
    public function testUserCanCreateTeamWithoutDescription(): void
    {
        list($token, $id) = $this->login();

        $request = [
            'name' => $this->request['name'],
            'created_by' => $id,
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson($this->teamURL, $request);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $request['name'],
                    'created_by' => $request['created_by'],
                ],
            ]);

        $this->assertDatabaseHas('teams', $request);
    }
}
