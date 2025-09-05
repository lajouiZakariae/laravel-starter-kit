<?php

use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\JWTAuthController;
use App\Http\Controllers\PasswordResetController;
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
Route::post('auth/register', [JWTAuthController::class, 'register'])->name('api.auth.register');

Route::post('auth/login', [JWTAuthController::class, 'login'])->name('api.auth.login');

Route::post('email/send', [EmailVerificationController::class, 'sendVerificationEmail'])->name('api.auth.email.send')->middleware('throttle:send-verification-email');

Route::post('email/verify', [EmailVerificationController::class, 'verifyEmail'])->name('api.auth.email.verify');

Route::post('password/send-reset', [PasswordResetController::class, 'sendPasswordResetCode'])->name('api.auth.password.send-reset')->middleware('throttle:send-password-reset');

Route::post('password/verify-reset', [PasswordResetController::class, 'verifyPasswordResetCode'])->name('api.auth.password.verify-reset');

Route::post('password/reset', [PasswordResetController::class, 'resetPassword'])->name('api.auth.password.reset');

// Protected routes (authentication required)
Route::middleware('auth:api')->group(function (): void {
    Route::post('auth/refresh', [JWTAuthController::class, 'refresh'])
        ->middleware('throttle:refresh')
        ->name('api.auth.refresh');

    Route::get('auth/me', [JWTAuthController::class, 'me']);
});
