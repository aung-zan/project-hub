<?php

namespace Tests\Feature\Team;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\FeatureTestCase;

class TeamStoreTest extends FeatureTestCase
{
    use RefreshDatabase;
    use TestHelper;

    protected string $url = 'http://localhost/api/teams';
    private array $request = [
        'name' => 'Testing Team',
        'description' => 'Team for testing.',
    ];

    /**
     * Test for user cannot create resource with empty data.
     */
    public function testUserCannotCreateAResourceWithEmptyData(): void
    {
        $request = [];

        list($token) = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson($this->url, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
            ]);
    }

    /**
     * Test for user cannot create team without name field.
     */
    public function testUserCannotCreateAResourceWithoutName(): void
    {
        list($token) = $this->login();

        $request = [
            'description' => $this->request['description'],
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson($this->url, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['name' => ['The name field is required.']],
            ]);
    }

    /**
     * Test for user can create team without description field.
     */
    public function testUserCanCreateAResourceWithoutDescription(): void
    {
        list($token) = $this->login();

        $request = [
            'name' => $this->request['name'],
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson($this->url, $request);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $request['name'],
                ],
            ]);

        $this->assertDatabaseHas('teams', $request);
    }
}
