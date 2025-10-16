<?php

namespace Tests\Feature\Profile;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;
    use TestHelper;

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
    public function testUserCannotAccessUpdateWithoutToken(): void
    {
        $request = [];
        $response = $this->putJson($this->profileURL, $request);

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
    public function testUserCannotAccessUpdateWithWrongToken(): void
    {
        $request = [];
        $token = 'abc';

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->profileURL, $request);

        $response->assertStatus(401)
            ->assertJsonFragments([
                ['success' => false,],
                ['error' => 'INVALID_TOKEN'],
                ['message' => 'Token is malformed or invalid.'],
            ]);
    }

    /**
     * Test for user can send empty request and the data are not changed.
     */
    public function testUserCanSendEmptyRequestToUpdate(): void
    {
        $request = [];
        $token = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->profileURL, $request);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $this->request['name'],
                    'username' => $this->request['username'],
                    'email' => $this->request['email'],
                ]
            ]);
    }

    /**
     * Test for user cannot update with a request with empty value.
     */
    public function testUserCannotUpdateEmptyValueRequest(): void
    {
        $request = [
            'name' => '',
            'password' => '',
        ];

        $token = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->profileURL, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['name' => ['The name field is required.']],
                ['password' => ['The password field is required.']],
            ]);
    }

    /**
     * Test for user cannot update password without confirm password.
     */
    public function testUserCannotUpdatePasswordWithoutConfirmPassword(): void
    {
        $request = [
            'name' => $this->request['name'],
            'password' => $this->request['password'],
        ];

        $token = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->profileURL, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['confirm_password' => ['The confirm password field is required when password is present.']],
            ]);
    }

    /**
     * Test for user cannot update other fields except from name and password.
     */
    public function testUserCannotUpdateExceptFromNameAndPassword(): void
    {
        $request = [
            'username' => $this->request['username'] . '1',
            'email' => 'test1@mail.com',
        ];

        $token = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->profileURL, $request);

        $response->assertStatus(200)
            ->assertJson([
                'success' => 'true',
                'data' => [
                    'username' => $this->request['username'],
                    'email' => $this->request['email'],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'username' => $this->request['username'],
            'email' => $this->request['email'],
        ]);
    }

    /**
     * Test for user can update name only.
     */
    public function testUserCanUpdateNameOnly(): void
    {
        $request = [
            'name' => $this->request['name'] . '1'
        ];

        $token = $this->login();

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->profileURL, $request);

        $response->assertStatus(200)
            ->assertJson([
                'success' => 'true',
                'data' => [
                    'name' => $request['name'],
                    'username' => $this->request['username'],
                    'email' => $this->request['email'],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $request['name'],
        ]);
    }
}
