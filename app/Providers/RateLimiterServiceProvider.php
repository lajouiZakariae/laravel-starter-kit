<?php

namespace App\Providers;

use App\Services\JWTAuthService;
use App\Services\RateLimiters\RateLimiterService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class RateLimiterServiceProvider extends ServiceProvider {
    /**
     * Register services.
     */
    public function register(): void {
        app()
            ->when(JWTAuthService::class)
            ->needs(RateLimiterService::class)
            ->give(fn (): RateLimiterService => (
                new RateLimiterService(
                    throttleName: 'login',
                    maxAttempts: config()->integer('jwt_auth.rate_limiting.login.max_attempts'),
                    decayMinutes: config()->integer('jwt_auth.rate_limiting.login.decay_minutes'),
                )
            ));
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {
        RateLimiter::for('refresh', function (Request $request): Limit {
            $refreshMaxAttemptsPerHour = config()->integer('jwt_auth.rate_limiting.refresh.max_attempts_per_hour');

            return Limit::perHour($refreshMaxAttemptsPerHour)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('send-verification-email', function (Request $request): Limit {
            $authUser = $request->user();

            $userEmail = $authUser ? $authUser->email : $request->string('email');

            $decayMinutes = config()->integer('jwt_auth.rate_limiting.send_verification_email.decay_minutes');

            $maxAttempts = config()->integer('jwt_auth.rate_limiting.send_verification_email.max_attempts');

            return Limit::perMinutes($decayMinutes, $maxAttempts)->by($userEmail);
        });

        RateLimiter::for('send-password-reset', function (Request $request): Limit {
            $userEmail = $request->string('email');

            $decayMinutes = config()->integer('jwt_auth.rate_limiting.send_password_reset.decay_minutes');

            $maxAttempts = config()->integer('jwt_auth.rate_limiting.send_password_reset.max_attempts');

            return Limit::perMinutes($decayMinutes, $maxAttempts)->by($userEmail);
        });
    }
}
