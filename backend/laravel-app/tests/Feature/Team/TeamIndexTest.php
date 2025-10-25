<?php

namespace Tests\Feature\Team;

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\TestCase;

class TeamIndexTest extends TestCase
{
    use RefreshDatabase;
    use TestHelper;

    private string $teamURL = 'http://localhost/api/teams';
    private array $teamData = [
        'name' => 'Testing Team',
        'description' => 'Team for testing.',
    ];

    /**
     * Test for user cannot access the data without jwt token.
     */
    public function testUserCannotAccessIndexWithoutToken(): void
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
    public function testUserCannotAccessIndexWithWrongToken(): void
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
     * Test for user access the data with right token.
     */
    public function testUserCanAccessIndexWithRightToken(): void
    {
        list($token, $id) = $this->login();

        $teamData = $this->teamData;
        $teamData['created_by'] = $id;

        Team::factory()->create($teamData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson($this->teamURL);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    [
                        'name' => $teamData['name'],
                        'description' => $teamData['description'],
                    ]
                ]
            ]);
    }
}
