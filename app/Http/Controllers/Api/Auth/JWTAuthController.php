<?php

namespace App\Http\Controllers\Api\Auth;

use App\Data\LoginCredentialsData;
use App\Data\RegisterUserData;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\JWTAuthService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * @tags Auth
 */
class JWTAuthController {
    public function __construct(
        private readonly JWTAuthService $authService
    ) {}

    /**
     * Register a new user
     */
    public function register(RegisterRequest $request): JsonResponse {
        $validatedPayload = $request->only(['first_name', 'last_name', 'email', 'password', 'phone_number_country_code', 'phone_number']);

        $registerUserData = RegisterUserData::from($validatedPayload);

        $authResultData = $this->authService->register($registerUserData);

        return UserResource::make($authResultData->user)
            ->additional([
                'meta' => [
                    'token' => $authResultData->token,
                    'token_type' => $authResultData->tokenType,
                    'expires_in' => $this->authService->getTokenExpirationTime(),
                ],
            ])
            ->toResponse($request)
            ->setStatusCode(SymfonyResponse::HTTP_CREATED);
    }

    /**
     * Login user and create token
     */
    public function login(LoginRequest $request): UserResource {
        /**
         * @var LoginCredentialsData
         */
        $credentials = LoginCredentialsData::from($request->only(['email', 'password']));

        $ipAddress = $request->ip();

        $result = $this->authService->login($credentials, $ipAddress);

        return UserResource::make($result->user)->additional([
            'meta' => [
                'token' => $result->token,
                'token_type' => $result->tokenType,
            ],
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me(): UserResource {
        $user = $this->authService->getAuthenticatedUser();

        return new UserResource($user);
    }

    /**
     * Refresh token
     */
    public function refresh(): UserResource {
        $result = $this->authService->refreshToken();

        return UserResource::make($result->user)->additional([
            'meta' => [
                'token' => $result->token,
                'token_type' => $result->tokenType,
            ],
        ]);
    }
}
