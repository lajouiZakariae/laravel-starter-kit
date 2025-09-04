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
            'max_attempts' => (int) env('JWT_LOGIN_MAX_ATTEMPTS', 5),
            'decay_minutes' => (int) env('JWT_LOGIN_DECAY_MINUTES', 1),
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
];
