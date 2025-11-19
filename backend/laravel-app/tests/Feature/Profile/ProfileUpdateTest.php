<?php

namespace Tests\Feature\Profile;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\TestHelper;
use Tests\FeatureTestCase;

class ProfileUpdateTest extends FeatureTestCase
{
    use RefreshDatabase;
    use TestHelper;

    protected string $method = 'put';
    protected string $url = 'http://localhost/api/profile';
    private array $userData = [
        'name' => 'test',
        'username' => 'tester',
        'email' => 'test@mail.com',
        'password' => 'password',
    ];
    private array $request = [
        'name' => 'Test',
        'password' => 'password',
    ];

    /**
     * Test for user can only send an empty request to update.
     * The data is not updated or changed.
     */
    public function testUserCanSendAnEmptyRequestToUpdate(): void
    {
        $request = [];
        $userData = $this->userData;

        list($token) = $this->login($userData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->url, $request);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => $userData['name'],
                    'username' => $userData['username'],
                    'email' => $userData['email'],
                ]
            ]);

        unset($userData['password']);

        $this->assertDatabaseHas('users', $userData);
    }

    /**
     * Test for user cannot update with a request with empty value.
     */
    public function testUserCannotUpdateAResourceWithEmptyData(): void
    {
        $userData = $this->userData;
        $request = [
            'name' => '',
            'password' => '',
        ];

        list($token) = $this->login($userData);

        unset($userData['password']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->url, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['name' => ['The name field is required.']],
                ['password' => ['The password field is required.']],
            ]);

        $this->assertDatabaseHas('users', $userData);
    }

    /**
     * Test for user cannot update password without confirm password.
     */
    public function testUserCannotUpdatePasswordWithoutConfirmPassword(): void
    {
        $userData = $this->userData;
        $request = $this->request;

        list($token) = $this->login($userData);

        unset($userData['password']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->url, $request);

        $response->assertStatus(422)
            ->assertJsonFragments([
                ['success' => false],
                ['error' => 'VALIDATION_FALIED'],
                ['confirm_password' => ['The confirm password field is required when password is present.']],
            ]);

        $this->assertDatabaseHas('users', $userData);
    }

    /**
     * Test for user can update only name and password.
     */
    public function testUserCanUpdateOnlyNameAndPassword(): void
    {
        $userData = $this->userData;
        $request = [
            'username' => $this->userData['username'] . '1',
            'email' => 'test1@mail.com',
        ];

        list($token) = $this->login($userData);

        unset($userData['password']);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->url, $request);

        $response->assertStatus(200)
            ->assertJson([
                'success' => 'true',
                'data' => [
                    'username' => $this->userData['username'],
                    'email' => $this->userData['email'],
                ],
            ]);

        $this->assertDatabaseHas('users', $userData);
    }

    /**
     * Test for user can update name only.
     */
    public function testUserCanUpdateNameOnly(): void
    {
        $userData = $this->userData;
        $request = [
            'name' => $this->request['name'],
        ];

        list($token) = $this->login($userData);

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->putJson($this->url, $request);

        $response->assertStatus(200)
            ->assertJson([
                'success' => 'true',
                'data' => [
                    'name' => $request['name'],
                    'username' => $userData['username'],
                    'email' => $userData['email'],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $request['name'],
        ]);
    }
}
