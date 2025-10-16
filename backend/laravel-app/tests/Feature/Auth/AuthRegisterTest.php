<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthRegisterTest extends TestCase
{
    use RefreshDatabase;

    private string $registerURL = 'http://localhost/api/register';
    private array $request = [
        'name' => 'test',
        'username' => 'tester',
        'email' => 'test@mail.com',
        'password' => 'password',
        'confirm_password' => 'password',
    ];

    /**
     * Test for creating a user with an empty data.
     */
    public function testUserCannotRegisterWithAnEmptyData(): void
    {
        $request = [];

        $response = $this->postJson($this->registerURL, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
            ]);
    }

    /**
     * Test for creating a user without confirm_password field.
     */
    public function testUserCannotRegisterWithoutConfirmPassword(): void
    {
        $request = $this->request;
        unset($request['confirm_password']);

        $response = $this->postJson($this->registerURL, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['confirm_password' => ['The confirm password field is required.']],
            ]);
    }

    /**
     * Test for creating a user with passwords mismatched.
     */
    public function testUserCannotRegisterWithMismatchedPasswords(): void
    {
        $request = $this->request;
        $request['confirm_password'] = 'pa';

        $response = $this->postJson($this->registerURL, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['confirm_password' => ['The confirm password field must match password.']],
            ]);
    }

    /**
     * Test for creating a user with the same email address.
     */
    public function testUserCannotRegisterWithSameEmail(): void
    {
        $request = $this->request;

        User::factory()->create([
            'email' => $request['email'],
        ]);

        $response = $this->postJson($this->registerURL, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['email' => ['The email has already been taken.']],
            ]);
    }

    /**
     * Test for creating a user with the same username.
     */
    public function testUserCannotRegisterWithSameUsername(): void
    {
        $request = $this->request;

        User::factory()->create([
            'username' => $request['username'],
        ]);

        $response = $this->postJson($this->registerURL, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['username' => ['The username has already been taken.']],
            ]);
    }

    /**
     * Test for creating a user with a unique data.
     */
    public function testUserCanRegisterWithUniqueData(): void
    {
        $request = $this->request;

        $response = $this->postJson($this->registerURL, $request);

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
