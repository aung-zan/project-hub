<?php

namespace Tests\Feature;

use App\Models\User;

trait TestHelper
{
    /**
     * Login and get JWT token
     *
     * @param array $credentails = []
     * @return array [token, id]
     */
    protected function login(array $credentails = []): array
    {
        if (empty($credentails)) {
            $credentails = [
                'email' => 'login@mail.com',
                'password' => 'password'
            ];
        }

        $user = User::factory()->create($credentails);

        /** @var string */
        $token = auth()->guard('api')->login($user);

        $this->actingAs($user, 'api');

        return [$token, $user->id];
    }
}
