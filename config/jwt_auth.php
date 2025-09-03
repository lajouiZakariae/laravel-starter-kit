<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JWT Auth Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for JWT authentication
    | including rate limiting settings for API endpoints.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for authentication endpoints.
    | These values can be overridden in your .env file.
    |
    */

    'rate_limiting' => [
        /*
        |--------------------------------------------------------------------------
        | Login Rate Limit
        |--------------------------------------------------------------------------
        |
        | Maximum number of login attempts per minute per IP address.
        | This helps prevent brute force attacks.
        |
        */
        'login' => [
            'max_attempts_per_hour' => (int) env('JWT_LOGIN_MAX_ATTEMPTS_PER_HOUR', 5),
        ],

        /*
        |--------------------------------------------------------------------------
        | Refresh Token Rate Limit
        |--------------------------------------------------------------------------
        |
        | Maximum number of token refresh attempts per minute per user.
        |
        */
        'refresh' => [
            'max_attempts_per_hour' => (int) env('JWT_REFRESH_MAX_ATTEMPTS_PER_HOUR', 10),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Configuration
    |--------------------------------------------------------------------------
    |
    | Configure token settings for JWT authentication.
    |
    */

    'token' => [
        /*
        |--------------------------------------------------------------------------
        | Token Name
        |--------------------------------------------------------------------------
        |
        | The name of the token that will be created for authentication.
        |
        */
        'name' => env('JWT_TOKEN_NAME', 'auth_token'),

        /*
        |--------------------------------------------------------------------------
        | Token Expiration
        |--------------------------------------------------------------------------
        |
        | The number of minutes after which the token will expire.
        | Set to null for no expiration.
        |
        */
        'expires_in' => env('JWT_TOKEN_EXPIRES_IN', 60 * 24), // 24 hours
    ],
];
