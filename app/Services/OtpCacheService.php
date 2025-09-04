<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class OtpCacheService {
    public function __construct(private readonly string $cacheKey) {}

    public function cacheOtpCodeForUser(User $user, string $otpCode): void {
        $cacheKey = $this->getCacheKeyForUser($user);

        $ttl = Carbon::now()->addMinutes(2);

        Cache::put($cacheKey, $otpCode, $ttl);
    }

    private function getCacheKeyForUser(User $user): string {
        return "{$this->cacheKey}_{$user->id}";
    }
}
