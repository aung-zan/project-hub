<?php

namespace Tests\Feature\Team;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\FeatureTestCase;
use Tests\TestCase;

class TeamUpdateTest extends FeatureTestCase
{
    use RefreshDatabase;
    use TestHelper;

    protected string $method = 'put';
    protected string $url = 'http://localhost/api/teams/{id}';
    private array $teamData = [
        'name' => 'testing Team',
        'description' => 'Team for testing.',
    ];
    private array $request = [
        'name' => 'Testing Team',
        'description' => 'A team for testing.',
    ];
    private array $search = ['{id}'];

    /**
     * Test for user cannot update a resource with wrong team id.
     */
    public function testUserCannotUpdateAResourceWithWrongId(): void
    {
        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->getRealURL($this->search, [1]), []);

        $response->assertStatus(404)
            ->assertJsonFragments([
                ['success' => false,],
                ['message' => 'Resource not found.'],
            ]);
    }

    /**
     * Test for user cannot update a resource with unauthorized team id.
     */
    public function testUserCannotUpdateAResourceWithUnauthorizedId(): void
    {
        $user = User::factory()->create();

        $teamData = $this->teamData;
        $teamData['created_by'] = $user->id;

        $team = Team::factory()->create($teamData);

        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->getRealURL($this->search, [$team->id]), []);

        $response->assertStatus(404)
            ->assertJsonFragments([
                ['success' => false,],
                ['message' => 'Resource not found.'],
            ]);
    }

    /**
     * Test for user can only send an empty request to update.
     * The data is not updated or changed.
     */
    public function testUserCanSendAnEmptyRequestToUpdate(): void
    {
        list($token, $id) = $this->login();

        $teamData = $this->teamData;
        $teamData['created_by'] = $id;

        $team = Team::factory()->create($teamData);

        $request = [];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->getRealURL($this->search, [$team->id]), $request);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $teamData['name'],
                    'description' => $teamData['description'],
                    'created_by' => $teamData['created_by'],
                ]
            ]);

        $this->assertDatabaseHas('teams', $teamData);
    }

    /**
     * Test for user cannot update a resource with empty data.
     */
    public function testUserCannotUpdateAResourceWithEmptyData(): void
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
            ->putJson($this->getRealURL($this->search, [$team->id]), $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['name' => ['The name field is required.']],
            ]);
    }

    /**
     * Test for user can update a resource with name only request.
     */
    public function testUserCanUpdateAResourceWithRightData(): void
    {
        list($token, $id) = $this->login();

        $teamData = $this->teamData;
        $teamData['created_by'] = $id;

        $team = Team::factory()->create($teamData);

        $request['name'] = $this->request['name'];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->getRealURL($this->search, [$team->id]), $request);

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
