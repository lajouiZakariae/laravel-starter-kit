<?php

use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\Auth\LinkEmailVerificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function (): void {
    // OTP Based Email Verification Routes
    Route::post('email/otp/send', [EmailVerificationController::class, 'sendVerificationEmail'])->name('email.send')->middleware('throttle:send-verification-email');

    Route::post('email/otp/verify', [EmailVerificationController::class, 'verifyEmail'])->name('email.verify');
});

// Link Based Email Verification Routes
Route::post('email/send-verification', [LinkEmailVerificationController::class, 'sendVerificationLink'])
    ->name('email.link.send')
    ->middleware('throttle:send-verification-link');

Route::post('email/verify', [LinkEmailVerificationController::class, 'verifyEmail'])
    ->name('email.link.verify')
    ->middleware('throttle:verify-email-link');
