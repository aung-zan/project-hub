<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfileShowTest extends TestCase
{
    use RefreshDatabase;

    private string $profileURL = 'http://localhost/api/profile';
    private array $request = [
        'name' => 'test',
        'username' => 'tester',
        'email' => 'test@mail.com',
        'password' => 'password',
    ];

    /**
     * Test for user cannot access the data without jwt token.
     */
    public function testUserCannotAccessShowWithoutToken(): void
    {
        $response = $this->getJson($this->profileURL);

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
    public function testUserCannotAccessShowWithWrongToken(): void
    {
        $token = 'abc';

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson($this->profileURL);

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
    public function testUserCanAccessShowWithRightToken(): void
    {
        $request = $this->request;

        $user = User::factory()->create($request);

        $token = auth()->guard('api')->login($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson($this->profileURL);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $request['name'],
                    'username' => $request['username'],
                    'email' => $request['email'],
                ]
            ]);
    }
}
