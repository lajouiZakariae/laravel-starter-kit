<?php

use App\Http\Controllers\Api\Auth\LinkBased\PasswordResetController;
use App\Http\Controllers\Api\Auth\OtpBased\OtpPasswordResetController;
use Illuminate\Support\Facades\Route;

// Reset Link based password reset routes
Route::post('password/send-reset', [PasswordResetController::class, 'sendPasswordResetCode'])
    ->name('password.send-reset')
    ->middleware('throttle:send-password-reset');

Route::post('password/reset', [PasswordResetController::class, 'resetPassword'])->name('password.reset');

// OTP based password reset routes
Route::post('password/otp/send-reset', [OtpPasswordResetController::class, 'sendPasswordResetCode'])->name('password.otp.send-reset')->middleware('throttle:send-password-reset');

Route::post('password/otp/verify-reset', [OtpPasswordResetController::class, 'verifyPasswordResetCode'])->name('password.otp.verify-reset');

Route::post('password/otp/reset', [OtpPasswordResetController::class, 'resetPassword'])->name('password.otp.reset');
