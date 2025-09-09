<?php

namespace App\Providers;

use App\Services\EmailVerificationService;
use App\Services\OtpCacheService;
use Illuminate\Support\ServiceProvider;

class OtpCacheServiceProvider extends ServiceProvider {
    /**
     * Register services.
     */
    public function register(): void {
        app()
            ->when(EmailVerificationService::class)
            ->needs(OtpCacheService::class)
            ->give(fn (): OtpCacheService => (
                new OtpCacheService(
                    cacheKey: 'email_verification',
                )
            ));
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {}
}
