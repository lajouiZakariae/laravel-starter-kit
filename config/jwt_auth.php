<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Extra JWT Auth Configuration
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Access Token & Refresh Token TTL
    |--------------------------------------------------------------------------
    |
    | The length of time (in minutes) that the access token is valid for.
    | Defaults to 2 hours (120 minutes).
    | The length of time (in minutes) that the refresh token is valid for.
    | Defaults to 7 days (10080 minutes).
    |
    */
    'ttl' => (int) env('JWT_TTL', 120),

    'refresh_token_ttl' => (int) env('JWT_REFRESH_TOKEN_TTL', 10080),

    /*
    |--------------------------------------------------------------------------
    | Email Verification Token TTL
    |--------------------------------------------------------------------------
    |
    | The length of time (in minutes) that the email verification token is
    | valid for. Defaults to 60 minutes.
    |
    */
    'email_verification_token_ttl' => (int) env('EMAIL_VERIFICATION_TOKEN_TTL', 60),

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
        | Maximum number of token refresh attempts per hour per user.
        |
        */
        'refresh' => [
            'max_attempts_per_hour' => (int) env('JWT_REFRESH_MAX_ATTEMPTS_PER_HOUR', 10),
        ],

        /*
        |--------------------------------------------------------------------------
        | Send Verification Email Rate Limit
        |--------------------------------------------------------------------------
        |
        | Maximum number of verification email requests per time period per user.
        |
        */
        'send_verification_email' => [
            'max_attempts' => (int) env('SEND_VERIFICATION_EMAIL_MAX_ATTEMPTS', 3),
            'decay_minutes' => (int) env('SEND_VERIFICATION_EMAIL_DECAY_MINUTES', 5),
        ],

        /*
        |--------------------------------------------------------------------------
        | Send Password Reset Rate Limit
        |--------------------------------------------------------------------------
        |
        | Maximum number of password reset requests per time period per user.
        |
        */
        'send_password_reset' => [
            'max_attempts' => (int) env('SEND_PASSWORD_RESET_MAX_ATTEMPTS', 3),
            'decay_minutes' => (int) env('SEND_PASSWORD_RESET_DECAY_MINUTES', 5),
        ],

        /*
        |--------------------------------------------------------------------------
        | Send Verification Link Rate Limit
        |--------------------------------------------------------------------------
        |
        | Maximum number of verification link requests per time period per email.
        |
        */
        'send_verification_link' => [
            'max_attempts' => (int) env('SEND_VERIFICATION_LINK_MAX_ATTEMPTS', 3),
            'decay_minutes' => (int) env('SEND_VERIFICATION_LINK_DECAY_MINUTES', 5),
        ],

        /*
        |--------------------------------------------------------------------------
        | Verify Email Link Rate Limit
        |--------------------------------------------------------------------------
        |
        | Maximum number of email link verification attempts per minute per IP.
        |
        */
        'verify_email_link' => [
            'max_attempts' => (int) env('VERIFY_EMAIL_LINK_MAX_ATTEMPTS', 10),
            'decay_minutes' => (int) env('VERIFY_EMAIL_LINK_DECAY_MINUTES', 1),
        ],
    ],
];
