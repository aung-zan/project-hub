<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthRegisterTest extends TestCase
{
    use RefreshDatabase;

    private string $url = 'http://localhost/api/register';
    private array $request = [
        'name' => 'test',
        'username' => 'tester',
        'email' => 'test@mail.com',
        'password' => 'password',
        'confirm_password' => 'password',
    ];

    /**
     * Test for user cannot register with an empty request.
     */
    public function testUserCannotRegisterWithAnEmptyRequest(): void
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
     * Test for user cannot register without confirm_password field.
     */
    public function testUserCannotRegisterWithoutConfirmPassword(): void
    {
        $request = $this->request;
        unset($request['confirm_password']);

        $response = $this->postJson($this->url, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['confirm_password' => ['The confirm password field is required.']],
            ]);
    }

    /**
     * Test for user cannot register with mismatched passwords.
     */
    public function testUserCannotRegisterWithMismatchedPasswords(): void
    {
        $request = $this->request;
        $request['confirm_password'] = 'pa';

        $response = $this->postJson($this->url, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['confirm_password' => ['The confirm password field must match password.']],
            ]);
    }

    /**
     * Test for user cannot register with an existing email.
     */
    public function testUserCannotRegisterWithExistingEmail(): void
    {
        $request = $this->request;

        User::factory()->create([
            'email' => $request['email'],
        ]);

        $response = $this->postJson($this->url, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['email' => ['The email has already been taken.']],
            ]);
    }

    /**
     * Test for user cannot register with an existing username.
     */
    public function testUserCannotRegisterWithExistingUsername(): void
    {
        $request = $this->request;

        User::factory()->create([
            'username' => $request['username'],
        ]);

        $response = $this->postJson($this->url, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['username' => ['The username has already been taken.']],
            ]);
    }

    /**
     * Test for user can register with unique right data.
     */
    public function testUserCanRegisterWithUniqueRightData(): void
    {
        $request = $this->request;

        $response = $this->postJson($this->url, $request);

        unset($request['password']);
        unset($request['confirm_password']);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'User register successfully.',
                'data' => $request
            ]);

        $this->assertDatabaseHas('users', $request);
    }
}
