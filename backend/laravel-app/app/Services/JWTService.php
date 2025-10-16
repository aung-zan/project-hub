<?php

namespace App\Services;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;

class JWTService
{
    private string $secret;
    private string $algorithm;
    private int $ttl;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->secret = config('jwt.secret');
        $this->algorithm = config('jwt.algo');
        $this->ttl = config('jwt.ttl');
    }

    /**
     * Generate the jwt token.
     *
     * @param int $userId
     * @param array $customClaims
     * @return string
     */
    public function generateToken(int $userId, array $customClaims): string
    {
        $issuedAt = time();

        $payload = array_merge([
            'sub' => $userId,
            'iat' => $issuedAt,
            'exp' => $issuedAt + $this->ttl,
            'nbf' => $issuedAt,
        ], $customClaims);

        return JWT::encode($payload, $this->secret, $this->algorithm);
    }

    /**
     * Verify the requested token.
     *
     * @param string $token
     * @return array ['valid', 'payload'] || ['valid', 'error', 'message']
     */
    public function verifyToken(string $token): array
    {
        $result = [];

        try {
            $decoded = JWT::decode($token, new Key($this->secret, $this->algorithm));

            return [
                'valid' => true,
                'payload' => (array) $decoded,
            ];
        } catch (\Throwable $th) {
            if ($th instanceof ExpiredException) {
                $result = [
                    'valid' => false,
                    'error' => 'TOKEN_EXPIRED',
                    'message' => 'The token has expired.'
                ];
            } elseif ($th instanceof SignatureInvalidException) {
                $result = [
                    'valid' => false,
                    'error' => 'INVALID_SIGNATURE',
                    'message' => 'Token signature is invalid.'
                ];
            } else {
                $result = [
                    'valid' => false,
                    'error' => 'INVALID_TOKEN',
                    'message' => 'Token is malformed or invalid.'
                ];
            }

            return $result;
        }
    }

    /**
     * Get user_id from the requested token.
     *
     * @param string $token
     * @return ?int
     */
    public function getUserIdFromToken(string $token): ?int
    {
        $result = $this->verifyToken($token);

        if (!$result['valid']) {
            return null;
        }

        return $result['payload']['sub'];
    }
}
