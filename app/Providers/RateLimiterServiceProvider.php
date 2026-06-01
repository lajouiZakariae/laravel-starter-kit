<?php

namespace App\Providers;

use App\Services\JWTAuthService;
use App\Services\RateLimiters\RateLimiterService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class RateLimiterServiceProvider extends ServiceProvider {
    /**
     * Register services.
     */
    public function register(): void {

        $configRepository = App::make(Repository::class);

        $this->app
            ->when(JWTAuthService::class)
            ->needs(RateLimiterService::class)
            ->give(fn (): RateLimiterService => (
                new RateLimiterService(
                    throttleName: 'login',
                    maxAttempts: $configRepository->integer('jwt_auth.rate_limiting.login.max_attempts'),
                    decayMinutes: $configRepository->integer('jwt_auth.rate_limiting.login.decay_minutes'),
                )
            ));
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {
        $configRepository = App::make(Repository::class);

        RateLimiter::for('refresh', function (Request $request) use ($configRepository): Limit {
            $refreshMaxAttemptsPerHour = $configRepository->integer('jwt_auth.rate_limiting.refresh.max_attempts_per_hour');

            return Limit::perHour($refreshMaxAttemptsPerHour)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('send-verification-email', function (Request $request) use ($configRepository): Limit {
            $authUser = $request->user();

            $userEmail = $authUser ? $authUser->email : $request->string('email');

            $decayMinutes = $configRepository->integer('jwt_auth.rate_limiting.send_verification_email.decay_minutes');

            $maxAttempts = $configRepository->integer('jwt_auth.rate_limiting.send_verification_email.max_attempts');

            return Limit::perMinutes($decayMinutes, $maxAttempts)->by($userEmail);
        });

        RateLimiter::for('send-password-reset', function (Request $request) use ($configRepository): Limit {
            $userEmail = $request->string('email');

            $decayMinutes = $configRepository->integer('jwt_auth.rate_limiting.send_password_reset.decay_minutes');

            $maxAttempts = $configRepository->integer('jwt_auth.rate_limiting.send_password_reset.max_attempts');

            return Limit::perMinutes($decayMinutes, $maxAttempts)->by($userEmail);
        });

        RateLimiter::for('send-verification-link', function (Request $request) use ($configRepository): Limit {
            $userEmail = $request->string('email');

            $decayMinutes = $configRepository->integer('jwt_auth.rate_limiting.send_verification_link.decay_minutes');

            $maxAttempts = $configRepository->integer('jwt_auth.rate_limiting.send_verification_link.max_attempts');

            return Limit::perMinutes($decayMinutes, $maxAttempts)->by($userEmail);
        });

        RateLimiter::for('verify-email-link', function (Request $request) use ($configRepository): Limit {
            $decayMinutes = $configRepository->integer('jwt_auth.rate_limiting.verify_email_link.decay_minutes');

            $maxAttempts = $configRepository->integer('jwt_auth.rate_limiting.verify_email_link.max_attempts');

            return Limit::perMinutes($decayMinutes, $maxAttempts)->by($request->ip());
        });
    }
}
