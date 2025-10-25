<?php

namespace Tests\Feature\TeamUser;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\TestCase;

class TeamUserStoreTest extends TestCase
{
    use RefreshDatabase;
    use TestHelper;

    private string $teamUserURL = 'http://localhost/api/teams/{id}/members';
    private array $teamData = [
        'name' => 'Testing team A'
    ];

    /**
     * Test for user cannot access store without jwt token.
     */
    public function testUserCannotAccessStoreWithoutToken(): void
    {
        $response = $this->postJson($this->teamUserURL, []);

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
            ->postJson($this->teamUserURL, []);

        $response->assertStatus(401)
            ->assertJsonFragments([
                ['success' => false,],
                ['error' => 'INVALID_TOKEN'],
                ['message' => 'Token is malformed or invalid.'],
            ]);
    }

    public function testUserCannotSendAnEmptyRequestToStore(): void
    {
        $request = [];
        $teamData = $this->teamData;

        list($token, $id) = $this->login();
        $teamData['created_by'] = $id;

        $team = Team::factory()->create($teamData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                str_replace('{id}', $team->id, $this->teamUserURL),
                $request
            );

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['member_id' => ['The member_id field is required.']],
            ]);
    }

    public function testUserCannotSendARequestWithoutAnArrayOfMemberId(): void
    {
        $request = ['member_id' => 1];
        $teamData = $this->teamData;

        list($token, $id) = $this->login();
        $teamData['created_by'] = $id;

        $team = Team::factory()->create($teamData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                str_replace('{id}', $team->id, $this->teamUserURL),
                $request
            );

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['member_id' => ['The member_id field must be an array.']],
            ]);
    }

    public function testUserCannotSendARequestWithStringofMemberId(): void
    {
        $request = ['member_id' => ['1', '2']];
        $teamData = $this->teamData;

        list($token, $id) = $this->login();
        $teamData['created_by'] = $id;

        $team = Team::factory()->create($teamData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                str_replace('{id}', $team->id, $this->teamUserURL),
                $request
            );

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['member_id.0' => ['The member_id field must be an integer.']],
                ['member_id.1' => ['The member_id field must be an integer.']],
            ]);
    }

    public function testUserCannotSendARequestWithFakeMemberId(): void
    {
        $request = ['member_id' => [10, 11]];
        $teamData = $this->teamData;

        list($token, $id) = $this->login();
        $teamData['created_by'] = $id;

        $team = Team::factory()->create($teamData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                str_replace('{id}', $team->id, $this->teamUserURL),
                $request
            );

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['member_id.0' => ['The selected member_id is invalid.']],
                ['member_id.1' => ['The selected member_id is invalid.']],
            ]);
    }

    public function testUserCanSendARequestWithExistIntegerMemberId(): void
    {
        $teamData = $this->teamData;

        list($token, $id) = $this->login();
        $teamData['created_by'] = $id;

        $team = Team::factory()->create($teamData);
        $user = User::factory()->create();

        $request = ['member_id' => [$id, $user->id]];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                str_replace('{id}', $team->id, $this->teamUserURL),
                $request
            );

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'members' => [$id, $user->id]
                ]
            ]);
    }
}
