<?php

namespace App\Services\RateLimiters;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\RateLimiter;

class LoginRateLimiterService {
    public function getLoginKey(?int $userId = null, ?string $ipAddress = null): string {
        $identifier = $userId ?: $ipAddress;

        return "login:$identifier";
    }

    public function isLoginRateLimited(?int $userId = null, ?string $ipAddress = null): bool {
        $key = $this->getLoginKey($userId, $ipAddress);

        $maxAttempts = config()->integer('jwt_auth.rate_limiting.login.max_attempts_per_hour');

        return RateLimiter::tooManyAttempts($key, $maxAttempts);
    }

    public function getLoginRetryAfter(?int $userId = null, ?string $ipAddress = null): int {
        $key = $this->getLoginKey($userId, $ipAddress);

        return RateLimiter::availableIn($key);
    }

    public function hitLoginRateLimit(?int $userId = null, ?string $ipAddress = null): void {
        $key = $this->getLoginKey($userId, $ipAddress);

        RateLimiter::hit($key, 3600);
    }

    public function clearLoginRateLimit(?int $userId = null, ?string $ipAddress = null): void {
        $key = $this->getLoginKey($userId, $ipAddress);

        RateLimiter::clear($key);
    }

    public function checkLoginRateLimit(?int $userId = null, ?string $ipAddress = null): void {
        if ($this->isLoginRateLimited($userId, $ipAddress)) {
            $availableIn = $this->getLoginRetryAfter($userId, $ipAddress);

            throw new HttpResponseException(
                response()->json([
                    'message' => __('auth.throttle', [
                        'seconds' => $availableIn,
                        'minutes' => ceil($availableIn / 60),
                    ]),
                    'errors' => [
                        'email' => [__('auth.throttle', [
                            'seconds' => $availableIn,
                            'minutes' => ceil($availableIn / 60),
                        ])],
                    ],
                    'retry_after' => $availableIn,
                ], 429)
            );
        }
    }

    public function getLoginAttemptCount(?int $userId = null, ?string $ipAddress = null): int {
        $key = $this->getLoginKey($userId, $ipAddress);

        return RateLimiter::attempts($key);
    }

    public function getRemainingLoginAttempts(?int $userId = null, ?string $ipAddress = null): int {
        $maxAttempts = config()->integer('jwt_auth.rate_limiting.login.max_attempts_per_hour');

        $currentAttempts = $this->getLoginAttemptCount($userId, $ipAddress);

        return max(0, $maxAttempts - $currentAttempts);
    }
}
