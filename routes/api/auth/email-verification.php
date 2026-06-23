<?php

use App\Http\Controllers\Api\Auth\LinkBased\EmailVerificationController;
use App\Http\Controllers\Api\Auth\OtpBased\OtpEmailVerificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function (): void {
    // OTP Based Email Verification Routes
    Route::post('email/otp/send', [OtpEmailVerificationController::class, 'sendVerificationEmail'])->name('email.send')->middleware('throttle:send-verification-email');

    Route::post('email/otp/verify', [OtpEmailVerificationController::class, 'verifyEmail'])->name('email.verify');
});

// Link Based Email Verification Routes
Route::post('email/send-verification', [EmailVerificationController::class, 'sendVerificationLink'])
    ->name('email.link.send')
    ->middleware('throttle:send-verification-email');

Route::post('email/verify', [EmailVerificationController::class, 'verifyEmailLink'])
    ->name('email.link.verify');
