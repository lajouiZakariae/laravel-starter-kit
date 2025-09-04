<?php

namespace App\Services\RateLimiters;

use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class RateLimiterService {
    public function __construct(
        private readonly string $throttleName,
        private readonly int $maxAttempts,
        private readonly int $decayMinutes,
    ) {}

    public function hitRateLimit(?string $identifier): void {
        $key = $this->getKey($identifier);

        RateLimiter::hit($key, $this->decayMinutes * 60);
    }

    public function checkRateLimit(?string $identifier): void {
        if ($this->isRateLimited($identifier)) {
            $availableIn = $this->getRetryAfterSecondsForIdentifier($identifier);

            throw new TooManyRequestsHttpException($availableIn);
        }
    }

    public function clearRateLimit(?string $identifier): void {
        $key = $this->getKey($identifier);

        RateLimiter::clear($key);
    }

    public function isRateLimited(?string $identifier): bool {
        $key = $this->getKey($identifier);

        return RateLimiter::tooManyAttempts($key, $this->maxAttempts);
    }

    public function getRetryAfterSeconds(?string $identifier): int {
        return $this->getRetryAfterSecondsForIdentifier($identifier);
    }

    public function getRetryAfterSecondsForIdentifier(?string $identifier): int {
        $key = $this->getKey($identifier);

        return RateLimiter::availableIn($key);
    }

    private function getKey(?string $identifier): string {
        return "{$this->throttleName}:$identifier";
    }
}
