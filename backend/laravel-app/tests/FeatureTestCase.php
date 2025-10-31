<?php

namespace Tests;

use Illuminate\Testing\TestResponse;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

abstract class FeatureTestCase extends TestCase
{
    protected string $url;
    protected string $method;

    /**
     * Test for user cannot access the data without jwt token.
     */
    public function testUserCannotAccessWithoutToken(): void
    {
        $response = $this->sendRequest();

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
    public function testUserCannotAccessWithWrongToken(): void
    {
        $token = 'abc';

        $response = $this->sendRequest($token);

        $response->assertStatus(401)
            ->assertJsonFragments([
                ['success' => false,],
                ['error' => 'INVALID_TOKEN'],
                ['message' => 'Token is malformed or invalid.'],
            ]);
    }

    protected function sendRequest(string $token = ''): TestResponse
    {
        $request = $token
            ? $this->withHeader('Authorization', "Bearer $token")
            : $this;

        return match ($this->method) {
            'get' => $request->getJson($this->url),
            'post' => $request->postJson($this->url),
            'put' => $request->putJson($this->url),
            'delete' => $request->deleteJson($this->url),
            default => throw new MethodNotAllowedException(
                ['get', 'post', 'put', 'delete'],
                'Undefined or unrecognize method.'
            ),
        };
    }
}
