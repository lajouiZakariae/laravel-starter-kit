<?php

namespace App\Http\Controllers\Api\Auth;

use App\Contracts\Http\ApiResponse;
use App\Data\LoginCredentialsData;
use App\Data\RegisterUserData;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\User\UserResource;
use App\Services\JWTAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @tags Auth
 */
class JWTAuthController {
    public function __construct(
        private readonly JWTAuthService $authService,
        private readonly ApiResponse $apiResponse,
    ) {}

    /**
     * Register a new user
     *
     * @response UserResource
     */
    public function register(RegisterRequest $request): JsonResponse {
        $registerUserData = RegisterUserData::from($request);

        $authResultData = $this->authService->register($registerUserData);

        return $this->apiResponse->createdResponse(new UserResource($authResultData->user), [
            'meta' => [
                'token' => $authResultData->token,
                'refresh_token' => $authResultData->refreshToken,
                'token_type' => $authResultData->tokenType,
                'expires_in' => $this->authService->getTokenExpirationTime(),
                'refresh_expires_in' => $this->authService->getRefreshTokenExpirationTime(),
            ],
        ]);
    }

    /**
     * Login user and create token
     *
     * @response UserResource
     */
    public function login(LoginRequest $request): JsonResponse {
        /**
         * @var LoginCredentialsData
         */
        $credentials = LoginCredentialsData::from($request->only(['email', 'password']));

        $ipAddress = $request->ip();

        $result = $this->authService->login($credentials, $ipAddress);

        return $this->apiResponse->successResponse(new UserResource($result->user), [
            'meta' => [
                'token' => $result->token,
                'refresh_token' => $result->refreshToken,
                'token_type' => $result->tokenType,
                'expires_in' => $this->authService->getTokenExpirationTime(),
                'refresh_expires_in' => $this->authService->getRefreshTokenExpirationTime(),
            ],
        ]);
    }

    /**
     * Get authenticated user
     *
     * @response UserResource
     */
    public function me(): JsonResponse {
        $user = $this->authService->getAuthenticatedUser();

        return $this->apiResponse->successResponse(new UserResource($user));
    }

    /**
     * Refresh access token using a refresh token
     *
     * @response UserResource
     */
    public function refresh(Request $request): JsonResponse {
        $request->validate([
            'refresh_token' => ['required', 'string'],
        ]);

        $result = $this->authService->refreshToken($request->string('refresh_token')->toString());

        return $this->apiResponse->successResponse(new UserResource($result->user), [
            'meta' => [
                'token' => $result->token,
                'refresh_token' => $result->refreshToken,
                'token_type' => $result->tokenType,
                'expires_in' => $this->authService->getTokenExpirationTime(),
                'refresh_expires_in' => $this->authService->getRefreshTokenExpirationTime(),
            ],
        ]);
    }
}
