<?php

// ============================================
// config/jwt.php
// ============================================

return [
    /*
    |--------------------------------------------------------------------------
    | JWT Secret Key
    |--------------------------------------------------------------------------
    |
    | The secret key used to sign the JWT tokens. Generate using:
    | openssl rand -base64 32
    |
    */
    'secret' => env('JWT_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | JWT Time to Live
    |--------------------------------------------------------------------------
    |
    | Specify the length of time (in seconds) that the token will be valid for.
    | Default is 1 hour (3600 seconds).
    |
    */
    'ttl' => env('JWT_TTL', 3600),

    /*
    |--------------------------------------------------------------------------
    | Refresh Time to Live
    |--------------------------------------------------------------------------
    |
    | Specify the length of time (in seconds) that the token can be refreshed.
    | Default is 2 weeks (1209600 seconds).
    |
    */
    'refresh_ttl' => env('JWT_REFRESH_TTL', 1209600),

    /*
    |--------------------------------------------------------------------------
    | JWT Algorithm
    |--------------------------------------------------------------------------
    |
    | Specify the algorithm used to sign the token.
    |
    */
    'algo' => env('JWT_ALGO', 'HS256'),
];
