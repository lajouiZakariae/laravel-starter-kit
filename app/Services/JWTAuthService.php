<?php

namespace App\Services;

use App\Contracts\UserContextInterface;
use App\Data\AuthResultData;
use App\Data\LoginCredentialsData;
use App\Data\Mail\UserMailData;
use App\Data\RegisterUserData;
use App\Models\User;
use App\Services\RateLimiters\RateLimiterService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTAuthService {
    public function __construct(
        private readonly UserContextInterface $userContext,
        private readonly RateLimiterService $loginRateLimiterService,
        private readonly UserMailerService $userMailerService,
    ) {}

    public function register(RegisterUserData $userData): AuthResultData {
        $user = User::create([
            'first_name' => $userData->first_name,
            'last_name' => $userData->last_name,
            'email' => $userData->email,
            'password' => Hash::make($userData->password),
            'phone_number' => $userData->phone_number->phoneNumber,
            'phone_number_country_code' => $userData->phone_number->iso2CountryCode,
        ]);

        $token = JWTAuth::fromUser($user);

        $userMailData = UserMailData::from($user);

        $this->userMailerService->sendUserRegisteredEmailForUser($userMailData);

        return AuthResultData::from([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function login(LoginCredentialsData $credentials, ?string $ipAddress = null): AuthResultData {
        $this->loginRateLimiterService->checkRateLimit($ipAddress);

        if (! $token = JWTAuth::attempt($credentials->toArray())) {
            $this->loginRateLimiterService->hitRateLimit($ipAddress);

            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        $this->loginRateLimiterService->clearRateLimit($ipAddress);

        $user = $this->userContext->getAuthenticatedUserOrFail();

        return AuthResultData::from([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function getAuthenticatedUser(): User {
        return $this->userContext->getAuthenticatedUserOrFail();
    }

    public function refreshToken(): AuthResultData {
        $newToken = JWTAuth::refresh();

        $user = $this->userContext->getAuthenticatedUserOrFail();

        return AuthResultData::from([
            'user' => $user,
            'token' => $newToken,
        ]);
    }

    public function getTokenExpirationTime(): int {
        return config()->integer('jwt.ttl') * 60;
    }
}
