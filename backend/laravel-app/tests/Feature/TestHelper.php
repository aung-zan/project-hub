<?php

namespace Tests\Feature;

use App\Models\User;

trait TestHelper
{
    /**
     * Login and get JWT token
     *
     * @return string JWT token
     */
    protected function login(): string
    {
        $request = $this->request;

        $user = User::factory()->create($request);

        /** @var string */
        $token = auth()->guard('api')->login($user);

        return $token;
    }
}
