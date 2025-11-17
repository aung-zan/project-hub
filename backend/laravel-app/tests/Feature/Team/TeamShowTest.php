<?php

namespace Tests\Feature\Team;

use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\FeatureTestCase;

class TeamShowTest extends FeatureTestCase
{
    use RefreshDatabase;
    use TestHelper;

    protected string $method = 'get';
    protected string $url = 'http://localhost/api/teams/';
    private array $teamData = [
        'name' => 'Testing Team',
        'description' => 'Team for testing.',
    ];

    /**
     * Test for user cannot get a resource with wrong team id.
     */
    public function testUserCannotGetAResourceWithWrongId(): void
    {
        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson($this->url . '1');

        $response->assertStatus(404)
            ->assertJsonFragments([
                ['success' => false,],
                ['message' => 'Resource not found.'],
            ]);
    }

    /**
     * Test for user cannot get a resource with unauthorized team id.
     */
    public function testUserCannotGetAResourceWithUnauthorizedId(): void
    {
        list($token, $id) = $this->login();

        $teamData = $this->teamData;
        $teamData['created_by'] = $id;

        $team = Team::factory()->create($teamData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson($this->url . $team->id);

        $response->assertStatus(404)
            ->assertJsonFragments([
                ['success' => false,],
                ['message' => 'Resource not found.'],
            ]);
    }

    /**
     * Test for user can get a resource with right team id.
     */
    public function testUserCanGetAResourceWithRightId(): void
    {
        list($token, $id) = $this->login();

        $teamData = $this->teamData;
        $teamData['created_by'] = $id;

        $team = Team::factory()->create($teamData);

        TeamUser::factory()->create([
            'team_id' => $team->id,
            'user_id' => $id,
            'created_by' => $id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson($this->url . $team->id);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $teamData['name'],
                    'description' => $teamData['description'],
                ]
            ]);
    }
}
