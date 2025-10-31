<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\FeatureTestCase;

class ProfileShowTest extends FeatureTestCase
{
    use RefreshDatabase;

    protected string $method = 'get';
    protected string $url = 'http://localhost/api/profile';
    private array $request = [
        'name' => 'test',
        'username' => 'tester',
        'email' => 'test@mail.com',
        'password' => 'password',
    ];

    /**
     * Test for user access the data with right token.
     */
    public function testUserCanGetAResourceWithRightToken(): void
    {
        $request = $this->request;

        $user = User::factory()->create($request);

        $token = auth()->guard('api')->login($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson($this->url);

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
