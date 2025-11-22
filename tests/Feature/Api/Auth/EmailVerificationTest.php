<?php

use App\Mail\EmailVerificationMail;
use App\Models\User;
use App\Services\OtpCacheService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

/**
 * @var \Tests\TestCase $this
 */
describe('Email Verification', function (): void {
    beforeEach(function (): void {
        Mail::fake();

        $this->otpCacheService = app(OtpCacheService::class, [
            'cacheKey' => 'email_verification',
        ]);
    });

    describe('Send Verification Email', function (): void {
        it('can send verification email for authenticated user', function (): void {
            $user = User::factory()->create([
                'email' => 'john.doe@example.com',
                'email_verified_at' => null,
            ]);

            $this->actingAs($user, 'api');

            $response = $this->post(route('api.auth.email.send'));

            $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Verification email sent',
                ]);

            Mail::assertSent(EmailVerificationMail::class);
        });

        it('fails to send verification email if email already verified', function (): void {
            $user = User::factory()->create([
                'email' => 'john.doe@example.com',
                'email_verified_at' => now(),
            ]);

            $this->actingAs($user, 'api');

            $response = $this->post(route('api.auth.email.send'));

            $response->assertStatus(400)
                ->assertJson([
                    'message' => 'Email already verified',
                ]);
        });

        it('respects rate limiting for send verification email', function (): void {
            $user = User::factory()->create([
                'email' => 'another.user@example.com',
                'email_verified_at' => null,
            ]);

            $this->actingAs($user, 'api');

            Collection::times(4, function (int $index): void {
                $response = $this->post(route('api.auth.email.send'));

                ($index < 4) ? $response->assertStatus(200) : $response->assertStatus(429);
            });
        });
    });

    describe('Verify Email', function (): void {
        it('can verify email for authenticated user with valid OTP', function (): void {
            $user = User::factory()->create([
                'email' => 'john.doe@example.com',
                'email_verified_at' => null,
            ]);

            $otpCode = '123456';

            $this->otpCacheService->cacheOtpCodeForUser($user, $otpCode);

            $this->actingAs($user, 'api');

            $response = $this->post(route('api.auth.email.verify'), [
                'email' => 'john.doe@example.com',
                'otp_code' => $otpCode,
            ]);

            $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Email verified successfully',
                ]);

            $user->refresh();

            expect($user->hasVerifiedEmail())->toBeTrue();
        });
    });

    describe('Authentication Context', function (): void {
        it('uses authenticated user when available for send verification', function (): void {
            $authenticatedUser = User::factory()->create([
                'email' => 'authenticated@example.com',
                'email_verified_at' => null,
            ]);

            $anotherUser = User::factory()->create([
                'email' => 'another@example.com',
                'email_verified_at' => null,
            ]);

            $this->actingAs($authenticatedUser, 'api');

            $response = $this->post(route('api.auth.email.send'), [
                'email' => $anotherUser->email,
            ]);

            $response->assertStatus(200);

            Mail::assertSent(EmailVerificationMail::class, fn (EmailVerificationMail $mail): bool => $mail->hasTo($authenticatedUser->email));
        });

        it('uses authenticated user when available for verify email', function (): void {
            $authenticatedUser = User::factory()->create([
                'email' => 'authenticated@example.com',
                'email_verified_at' => null,
            ]);

            $anotherUser = User::factory()->create([
                'email' => 'another@example.com',
                'email_verified_at' => null,
            ]);

            $this->actingAs($authenticatedUser, 'api');

            $otpCode = '123456';

            $this->otpCacheService->cacheOtpCodeForUser($authenticatedUser, $otpCode);

            // Even though we pass another email, it should use the authenticated user
            $response = $this->post(route('api.auth.email.verify'), [
                'email' => $anotherUser->email,
                'otp_code' => $otpCode,
            ]);

            $response->assertStatus(200);

            $authenticatedUser->refresh();

            expect($authenticatedUser->hasVerifiedEmail())->toBeTrue();

            expect($anotherUser->hasVerifiedEmail())->toBeFalse();
        });
    });
})->group('email-verification');
