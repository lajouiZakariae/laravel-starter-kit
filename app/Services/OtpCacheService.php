<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class OtpCacheService {
    public function __construct(private readonly string $cacheKey) {}

    public function cacheOtpCodeForUser(User $user, string $otpCode): void {
        $cacheKey = $this->getCacheKeyForUser($user);

        $ttl = Date::now()->addMinutes(2);

        Cache::put($cacheKey, $otpCode, $ttl);
    }

    public function getOtpCodeForUser(User $user): string {
        $cacheKey = $this->getCacheKeyForUser($user);

        $otpFromCache = Cache::get($cacheKey);

        if (blank($otpFromCache) || ! is_string($otpFromCache)) {
            throw new BadRequestHttpException('OTP code not found');
        }

        return $otpFromCache;
    }

    public function deleteOtpCodeForUser(User $user): void {
        $cacheKey = $this->getCacheKeyForUser($user);

        Cache::forget($cacheKey);
    }

    private function getCacheKeyForUser(User $user): string {
        return "{$this->cacheKey}_{$user->id}";
    }
}
