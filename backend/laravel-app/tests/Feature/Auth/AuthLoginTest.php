<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    private string $loginURL = 'http://localhost/api/login';
    private array $request = [
        'email' => 'test@mail.com',
        'password' => 'password',
    ];

    /**
     * Test for login a user with an empty data.
     */
    public function testUserCannotLoginWithAnEmptyData(): void
    {
        $request = [];

        $response = $this->postJson($this->loginURL, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['email' => ['The email field is required.']],
                ['password' => ['The password field is required.']],
            ]);
    }

    /**
     * Test for login a user with wrong credentials.
     */
    public function testUserCannotLoginWithWrongCredentials(): void
    {
        $request = $this->request;

        $response = $this->postJson($this->loginURL, $request);

        $response->assertStatus(401)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'UNAUTHORIZED_ACCESS'],
                ['message' => 'Email or password is wrong.'],
            ]);
    }

    /**
     * Test for login a user with right credentials.
     */
    public function testUserCanLoginWithRightCredentials(): void
    {
        $request = $this->request;

        User::factory()->create($request);

        $response = $this->postJson($this->loginURL, $request);

        $response->assertStatus(200)
            ->assertJsonFragments([
                ['success' => true],
                ['message' => 'Login successfully.'],
                ['token_type' => 'bearer'],
            ]);
    }
}
