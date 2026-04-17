<?php

namespace App\Services;

use App\Contracts\UserContext;
use App\Data\AuthResultData;
use App\Data\LoginCredentialsData;
use App\Data\Mail\UserMailData;
use App\Data\RegisterUserData;
use App\Enums\Role\UserRoleEnum;
use App\Models\User;
use App\Services\RateLimiters\RateLimiterService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Events\Dispatcher;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTAuthService {
    public function __construct(
        private readonly UserContext $userContext,
        private readonly RateLimiterService $loginRateLimiterService,
        private readonly UserMailerService $userMailerService,
        private readonly Hasher $hasher,
        private readonly Dispatcher $dispatcher,
        private readonly Repository $repository,
        private readonly UserService $userService,
    ) {}

    public function register(RegisterUserData $userData): AuthResultData {
        $user = User::query()->create([
            'first_name' => $userData->first_name,
            'last_name' => $userData->last_name,
            'email' => $userData->email,
            'password' => $this->hasher->make($userData->password),
            'phone_number' => $userData->phone_number,
        ]);

        $this->dispatcher->dispatch(new Registered($user));

        $userMailData = UserMailData::from($user);

        $this->userMailerService->sendUserRegisteredEmailForUser($userMailData);

        $this->userService->loadRelations($user);

        return AuthResultData::from([
            'user' => $user,
            'token' => $this->generateAccessToken($user, UserRoleEnum::CLIENT->value),
            'refresh_token' => $this->generateRefreshToken($user, UserRoleEnum::CLIENT->value),
        ]);
    }

    public function login(LoginCredentialsData $credentials, ?string $ipAddress = null): AuthResultData {
        $this->loginRateLimiterService->checkRateLimit($ipAddress);

        if (! JWTAuth::attempt(['email' => $credentials->email, 'password' => $credentials->password])) {
            $this->loginRateLimiterService->hitRateLimit($ipAddress);

            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        $this->loginRateLimiterService->clearRateLimit($ipAddress);

        $user = $this->userContext->getAuthenticatedUserOrFail();

        $isValidRole = match ($credentials->signed_in_as) {
            UserRoleEnum::CLIENT => $user->hasRole(UserRoleEnum::CLIENT->value),
            UserRoleEnum::PROFESSIONAL => $user->hasAnyRole([UserRoleEnum::PROFESSIONAL->value, UserRoleEnum::COLLABORATOR->value]),
        };

        if (! $isValidRole) {
            throw ValidationException::withMessages([
                'signed_in_as' => [__('auth.role_mismatch', ['role' => $credentials->signed_in_as->value])],
            ]);
        }

        $this->userService->loadRelations($user);

        return AuthResultData::from([
            'user' => $user,
            'token' => $this->generateAccessToken($user, $credentials->signed_in_as->value),
            'refresh_token' => $this->generateRefreshToken($user, $credentials->signed_in_as->value),
        ]);
    }

    public function getAuthenticatedUser(): User {
        $user = $this->userContext->getAuthenticatedUserOrFail();

        return $this->userService->loadRelations($user);
    }

    public function refreshToken(string $rawRefreshToken): AuthResultData {
        try {
            $payload = JWTAuth::setToken($rawRefreshToken)->getPayload();
        } catch (JWTException) {
            throw ValidationException::withMessages([
                'refresh_token' => [__('auth.invalid_refresh_token')],
            ]);
        }

        if ($payload->get('typ') !== 'refresh') {
            throw ValidationException::withMessages([
                'refresh_token' => [__('auth.invalid_refresh_token')],
            ]);
        }

        $user = User::query()->findOrFail($payload->get('sub'));

        $signedInAs = $payload->get('signed_in_as', '');

        JWTAuth::setToken($rawRefreshToken)->invalidate();

        $this->userService->loadRelations($user);

        return AuthResultData::from([
            'user' => $user,
            'token' => $this->generateAccessToken($user, $signedInAs),
            'refresh_token' => $this->generateRefreshToken($user, $signedInAs),
        ]);
    }

    public function getTokenExpirationTime(): int {
        return $this->repository->integer('jwt.ttl') * 60;
    }

    public function getRefreshTokenExpirationTime(): int {
        return $this->repository->integer('jwt_auth.refresh_token_ttl') * 60;
    }

    public function generateAccessToken(User $user, string $signedInAs): string {
        JWTAuth::factory()->setTTL($this->repository->integer('jwt.ttl'));

        return JWTAuth::customClaims(['signed_in_as' => $signedInAs])->fromUser($user);
    }

    public function generateRefreshToken(User $user, string $signedInAs): string {
        JWTAuth::factory()->setTTL($this->repository->integer('jwt_auth.refresh_token_ttl'));

        $token = JWTAuth::customClaims(['typ' => 'refresh', 'signed_in_as' => $signedInAs])->fromUser($user);

        // Reset so TTL/claims don't leak into subsequent token generation
        JWTAuth::factory()
            ->setTTL($this->repository->integer('jwt.ttl'))
            ->setCustomClaims([]);

        return $token;
    }
}
