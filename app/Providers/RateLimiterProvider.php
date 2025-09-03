<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class RateLimiterProvider extends ServiceProvider {
    /**
     * Register services.
     */
    public function register(): void {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {
        RateLimiter::for('refresh', function (Request $request): Limit {
            $refreshMaxAttemptsPerHour = config()->integer('jwt_auth.rate_limiting.refresh.max_attempts_per_hour');

            return Limit::perHour($refreshMaxAttemptsPerHour)->by($request->user()?->id ?: $request->ip());
        });
    }
}
