<?php

namespace App\Http\Controllers\Api\Auth;

use App\Concerns\ApiResponse;
use App\Data\LoginCredentialsData;
use App\Data\RegisterUserData;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\User\UserResource;
use App\Services\JWTAuthService;
use Illuminate\Http\JsonResponse;

/**
 * @tags Auth
 */
class JWTAuthController {
    use ApiResponse;

    public function __construct(
        private readonly JWTAuthService $authService
    ) {}

    /**
     * Register a new user
     */
    public function register(RegisterRequest $request): JsonResponse {
        $registerUserData = RegisterUserData::from($request);

        $authResultData = $this->authService->register($registerUserData);

        return $this->createdResponse(new UserResource($authResultData->user), [
            'meta' => [
                'token' => $authResultData->token,
                'token_type' => $authResultData->tokenType,
                'expires_in' => $this->authService->getTokenExpirationTime(),
            ],
        ]);
    }

    /**
     * Login user and create token
     */
    public function login(LoginRequest $request): JsonResponse {
        /**
         * @var LoginCredentialsData
         */
        $credentials = LoginCredentialsData::from($request->only(['email', 'password']));

        $ipAddress = $request->ip();

        $result = $this->authService->login($credentials, $ipAddress);

        return $this->successResponse(new UserResource($result->user), [
            'meta' => [
                'token' => $result->token,
                'token_type' => $result->tokenType,
            ],
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me(): JsonResponse {
        $user = $this->authService->getAuthenticatedUser();

        return $this->successResponse(new UserResource($user));
    }

    /**
     * Refresh token
     */
    public function refresh(): JsonResponse {
        $result = $this->authService->refreshToken();

        return $this->successResponse(new UserResource($result->user), [
            'meta' => [
                'token' => $result->token,
                'token_type' => $result->tokenType,
            ],
        ]);
    }
}
