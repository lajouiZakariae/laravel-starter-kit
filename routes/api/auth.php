<?php

use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\Auth\JWTAuthController;
use App\Http\Controllers\Api\Auth\OtpPasswordResetController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
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

Route::post('password/send-reset', [PasswordResetController::class, 'sendPasswordResetCode'])
    ->name('password.send-reset')
    ->middleware('throttle:send-password-reset');

Route::post('password/reset', [PasswordResetController::class, 'resetPassword'])->name('password.reset');

Route::post('password/otp/send-reset', [OtpPasswordResetController::class, 'sendPasswordResetCode'])->name('password.otp.send-reset')->middleware('throttle:send-password-reset');

Route::post('password/otp/verify-reset', [OtpPasswordResetController::class, 'verifyPasswordResetCode'])->name('password.otp.verify-reset');

Route::post('password/otp/reset', [OtpPasswordResetController::class, 'resetPassword'])->name('password.otp.reset');

// Protected routes (authentication required)
Route::middleware('auth:api')->group(function (): void {
    Route::post('refresh', [JWTAuthController::class, 'refresh'])
        ->middleware('throttle:refresh')
        ->name('refresh');

    Route::get('me', [JWTAuthController::class, 'me']);

    Route::post('email/otp/send', [EmailVerificationController::class, 'sendVerificationEmail'])->name('email.send')->middleware('throttle:send-verification-email');

    Route::post('email/otp/verify', [EmailVerificationController::class, 'verifyEmail'])->name('email.verify');
});
