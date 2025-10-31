<?php

namespace Tests\Feature\Team;

use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\FeatureTestCase;

class TeamIndexTest extends FeatureTestCase
{
    use RefreshDatabase;
    use TestHelper;

    protected string $method = 'get';
    protected string $url = 'http://localhost/api/teams';
    private array $teamData = [
        'name' => 'Testing Team',
        'description' => 'Team for testing.',
    ];

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
            ->getJson($this->url);

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
