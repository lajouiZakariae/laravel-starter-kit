<?php

use App\Http\Controllers\Api\Auth\JWTAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| JWT Authentication Routes
|--------------------------------------------------------------------------
|
| These routes handle JWT authentication including login, register,
| logout, refresh token, and user profile.
|
*/

// Public routes (no authentication required)
Route::post('register', [JWTAuthController::class, 'register'])->name('register');

Route::post('login', [JWTAuthController::class, 'login'])->name('login');

// Protected routes (authentication required)
Route::middleware('auth:api')->group(function (): void {
    Route::post('refresh', [JWTAuthController::class, 'refresh'])
        ->middleware('throttle:refresh')
        ->name('refresh');

    Route::get('user', [JWTAuthController::class, 'me'])->name('user');
});

require __DIR__ . '/password-reset.php';

require __DIR__ . '/email-verification.php';
