<?php

use App\Http\Controllers\Api\Auth\EmailVerificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function (): void {
    // OTP Based Email Verification Routes
    Route::post('email/otp/send', [EmailVerificationController::class, 'sendVerificationEmail'])->name('email.send')->middleware('throttle:send-verification-email');

    Route::post('email/otp/verify', [EmailVerificationController::class, 'verifyEmail'])->name('email.verify');
});
