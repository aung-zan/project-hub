<?php

namespace App\Guards;

use App\Services\JWTService;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

class JWTGuard implements Guard
{
    use GuardHelpers;

    // provider and user are in trait.
    protected Request $request;
    protected JWTService $jwtService;
    protected ?string $token = null;

    /**
     * Create a new class instance.
     */
    public function __construct(UserProvider $provider, Request $request, JWTService $jwtService)
    {
        $this->provider = $provider;
        $this->request = $request;
        $this->jwtService = $jwtService;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $token = $this->getTokenFromRequest();
        if (!$token) {
            return null;
        }

        $userId = $this->jwtService->getUserIdFromToken($token);

        return !$userId ? null : $this->user = $this->provider->retrieveById($userId);
    }

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     * @return bool
     */
    public function validate(array $credentials = []): bool
    {
        if (empty($credentials['email']) || empty($credentials['password'])) {
            return false;
        }

        $user = $this->provider->retrieveByCredentials($credentials);

        return $user && $this->provider->validateCredentials($user, $credentials);
    }

    /**
     * Validate a user's credentials and authenticate.
     *
     * @param array $credentials
     * @return bool|string
     */
    public function attempt(array $credentials = []): bool|string
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        if ($user && $this->provider->validateCredentials($user, $credentials)) {
            $this->setUser($user);

            return $this->jwtService->generateToken($user->id, $user->getJWTCustomClaims());
        }

        return false;
    }

    /**
     * Generate token for user.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return string
     */
    public function login(Authenticatable $user): string
    {
        $this->setUser($user);

        return $this->jwtService->generateToken($user->id, $user->getJWTCustomClaims());
    }

    /**
     * Get the token from the request header or url.
     *
     * @return ?string
     */
    protected function getTokenFromRequest(): ?string
    {
        if ($this->token) {
            return $this->token;
        }

        $token = $this->request->bearerToken();

        if (!$token) {
            $token = $this->request->query('token');
        }

        return $this->token = $token;
    }
}
