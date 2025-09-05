<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class OtpCacheService {
    public function __construct(private readonly string $cacheKey) {}

    public function cacheOtpCodeForUser(User $user, string $otpCode): void {
        $cacheKey = $this->getCacheKeyForUser($user);

        $ttl = Carbon::now()->addMinutes(2);

        Cache::put($cacheKey, $otpCode, $ttl);
    }

    public function getOtpCodeForUser(User $user): string {
        $cacheKey = $this->getCacheKeyForUser($user);

        $otpFromCache = Cache::get($cacheKey);

        if (! filled($otpFromCache) || ! is_string($otpFromCache)) {
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
