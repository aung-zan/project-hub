<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    private string $url = 'http://localhost/api/login';
    private array $request = [
        'email' => 'test@mail.com',
        'password' => 'password',
    ];

    /**
     * Test for user cannot login with an empty request.
     */
    public function testUserCannotLoginWithAnEmptyRequest(): void
    {
        $request = [];

        $response = $this->postJson($this->url, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['email' => ['The email field is required.']],
                ['password' => ['The password field is required.']],
            ]);
    }

    /**
     * Test for user cannot login with wrong credentials.
     */
    public function testUserCannotLoginWithWrongCredentials(): void
    {
        $request = $this->request;

        $response = $this->postJson($this->url, $request);

        $response->assertStatus(401)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'UNAUTHORIZED_ACCESS'],
                ['message' => 'Email or password is wrong.'],
            ]);
    }

    /**
     * Test for user cannot login with right credentials.
     */
    public function testUserCanLoginWithRightCredentials(): void
    {
        $request = $this->request;

        User::factory()->create($request);

        $response = $this->postJson($this->url, $request);

        $response->assertStatus(200)
            ->assertJsonFragments([
                ['success' => true],
                ['message' => 'Login successfully.'],
                ['token_type' => 'bearer'],
            ]);
    }
}
