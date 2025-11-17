<?php

namespace Tests\Feature\TeamUser;

use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\FeatureTestCase;

class TeamUserStoreTest extends FeatureTestCase
{
    use RefreshDatabase;
    use TestHelper;

    protected string $method = 'post';
    protected string $url = 'http://localhost/api/teams/{id}/members';
    private array $teamData = [
        'name' => 'Testing team A'
    ];

    /**
     * Test for user cannot send an empty request to store.
     */
    public function testUserCannotCreateAResourceWithEmptyData(): void
    {
        $request = [];
        $teamData = $this->teamData;

        list($token, $id) = $this->login();
        $teamData['created_by'] = $id;

        $team = Team::factory()->create($teamData);

        TeamUser::factory()->create([
            'team_id' => $team->id,
            'user_id' => $id,
            'created_by' => $id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                str_replace('{id}', $team->id, $this->url),
                $request
            );

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['member_id' => ['The member_id field is required.']],
            ]);
    }

    /**
     * Test for user cannot send a request without members' ids.
     */
    public function testUserCannotCreateAResourceWithoutAnArrayOfMemberId(): void
    {
        $request = ['member_id' => 1];
        $teamData = $this->teamData;

        list($token, $id) = $this->login();
        $teamData['created_by'] = $id;

        $team = Team::factory()->create($teamData);

        TeamUser::factory()->create([
            'team_id' => $team->id,
            'user_id' => $id,
            'created_by' => $id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                str_replace('{id}', $team->id, $this->url),
                $request
            );

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['member_id' => ['The member_id field must be an array.']],
            ]);
    }

    /**
     * Test for user cannot send a request with string type member's ids.
     */
    public function testUserCannotCreateAResourceWithStringofMemberId(): void
    {
        $request = ['member_id' => ['1', '2']];
        $teamData = $this->teamData;

        list($token, $id) = $this->login();
        $teamData['created_by'] = $id;

        $team = Team::factory()->create($teamData);

        TeamUser::factory()->create([
            'team_id' => $team->id,
            'user_id' => $id,
            'created_by' => $id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                str_replace('{id}', $team->id, $this->url),
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

    /**
     * Test for user cannot send a request with fake members' ids.
     */
    public function testUserCannotCreateAResourceWithFakeMemberId(): void
    {
        $request = ['member_id' => [10, 11]];
        $teamData = $this->teamData;

        list($token, $id) = $this->login();
        $teamData['created_by'] = $id;

        $team = Team::factory()->create($teamData);

        TeamUser::factory()->create([
            'team_id' => $team->id,
            'user_id' => $id,
            'created_by' => $id,
        ]);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                str_replace('{id}', $team->id, $this->url),
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

    /**
     * Test for user can send a request with right data.
     */
    public function testUserCanSendARequestWithExistIntegerMemberId(): void
    {
        $teamData = $this->teamData;

        list($token, $id) = $this->login();
        $teamData['created_by'] = $id;

        $team = Team::factory()->create($teamData);

        TeamUser::factory()->create([
            'team_id' => $team->id,
            'user_id' => $id,
            'created_by' => $id,
        ]);

        $user = User::factory()->create();

        $request = ['member_id' => [$id, $user->id]];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson(
                str_replace('{id}', $team->id, $this->url),
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
