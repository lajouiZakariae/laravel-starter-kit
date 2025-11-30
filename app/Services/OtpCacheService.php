<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Cache\CacheManager;
use Illuminate\Support\Facades\Date;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class OtpCacheService {
    public function __construct(private readonly string $cacheKey, private readonly CacheManager $cacheManager) {}

    public function cacheOtpCodeForUser(User $user, string $otpCode): void {
        $cacheKey = $this->getCacheKeyForUser($user);

        $ttl = Date::now()->addMinutes(2);

        $this->cacheManager->put($cacheKey, $otpCode, $ttl);
    }

    public function getOtpCodeForUser(User $user): string {
        $cacheKey = $this->getCacheKeyForUser($user);

        $otpFromCache = $this->cacheManager->get($cacheKey);

        if (blank($otpFromCache) || ! is_string($otpFromCache)) {
            throw new BadRequestHttpException('OTP code not found');
        }

        return $otpFromCache;
    }

    public function deleteOtpCodeForUser(User $user): void {
        $cacheKey = $this->getCacheKeyForUser($user);

        $this->cacheManager->forget($cacheKey);
    }

    private function getCacheKeyForUser(User $user): string {
        return "{$this->cacheKey}_{$user->id}";
    }
}
