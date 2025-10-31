<?php

namespace Tests\Feature\Team;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\FeatureTestCase;
use Tests\TestCase;

class TeamDestroyTest extends FeatureTestCase
{
    use RefreshDatabase;
    use TestHelper;

    protected string $method = 'delete';
    protected string $url = 'http://localhost/api/teams/{id}';
    private array $teamData = [
        'name' => 'Testing Team',
        'description' => 'Team for testing.',
    ];
    private array $search = ['{id}'];

    /**
     * Test for user cannot delete a resource with wrong team id.
     */
    public function testUserCannotDeleteAResourceWithWrongId(): void
    {
        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->getRealURL($this->search, [1]));

        $response->assertStatus(404)
            ->assertJsonFragments([
                ['success' => false,],
                ['message' => 'Resource not found.'],
            ]);
    }

    /**
     * Test for user cannot delete a resource with unauthorized team id.
     */
    public function testUserCannotDeleteAResourceWithUnauthorizedId(): void
    {
        $user = User::factory()->create();

        $teamData = $this->teamData;
        $teamData['created_by'] = $user->id;

        $team = Team::factory()->create($teamData);

        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->getRealURL($this->search, [$team->id]));

        $response->assertStatus(404)
            ->assertJsonFragments([
                ['success' => false,],
                ['message' => 'Resource not found.'],
            ]);
    }

    /**
     * Test for user can delete a resource with right team id.
     */
    public function testUserCanDeleteAResourceWithRightId(): void
    {
        list($token, $id) = $this->login();

        $teamData = $this->teamData;
        $teamData['created_by'] = $id;

        $team = Team::factory()->create($teamData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->deleteJson($this->getRealURL($this->search, [$team->id]));

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
