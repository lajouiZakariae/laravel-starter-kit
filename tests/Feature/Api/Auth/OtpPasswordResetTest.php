<?php

use App\Mail\OtpPasswordResetMail;
use App\Models\User;
use App\Services\OtpCacheService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/**
 * @var \Tests\ApiTestCase $this
 */
describe('Password Reset', function (): void {
    beforeEach(function (): void {
        Mail::fake();

        $this->otpCacheService = App::make(OtpCacheService::class, [
            'cacheKey' => 'password_reset',
        ]);
    });

    describe('sendPasswordResetCode', function (): void {
        it('can send password reset code for existing user', function (): void {
            User::factory()->create([
                'email' => 'john.doe@example.com',
            ]);

            $response = $this->postJson(route('api.auth.password.otp.send-reset'), [
                'email' => 'john.doe@example.com',
            ]);

            $response->assertStatus(200)
                ->assertJson(['message' => 'Password reset code sent']);

            Mail::assertSent(OtpPasswordResetMail::class);
        });
    });

    describe('verifyPasswordResetCode', function (): void {
        it('can verify password reset code with valid OTP', function (): void {
            $user = User::factory()->create([
                'email' => 'john.doe@example.com',
            ]);

            $otpCode = '123456';

            $this->otpCacheService->cacheOtpCodeForUser($user, $otpCode);

            $response = $this->postJson(route('api.auth.password.otp.verify-reset'), [
                'email' => 'john.doe@example.com',
                'otp_code' => $otpCode,
            ]);

            $response->assertStatus(200)->assertJson(['message' => 'Password reset code is valid']);
        });

        it('fails to verify password reset code with invalid OTP', function (): void {
            $user = User::factory()->create([
                'email' => 'john.doe@example.com',
            ]);

            $validOtpCode = '123456';

            $invalidOtpCode = '654321';

            $this->otpCacheService->cacheOtpCodeForUser($user, $validOtpCode);

            $response = $this->postJson(route('api.auth.password.otp.verify-reset'), [
                'email' => 'john.doe@example.com',
                'otp_code' => $invalidOtpCode,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['otp_code']);
        });

        it('validates OTP code length', function (): void {
            User::factory()->create([
                'email' => 'john.doe@example.com',
            ]);

            $response = $this->postJson(route('api.auth.password.otp.verify-reset'), [
                'email' => 'john.doe@example.com',
                'otp_code' => '12345',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['otp_code']);

            $response = $this->postJson(route('api.auth.password.otp.verify-reset'), [
                'email' => 'john.doe@example.com',
                'otp_code' => '1234567',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['otp_code']);

            $response = $this->postJson(route('api.auth.password.otp.verify-reset'), [
                'email' => 'john.doe@example.com',
                'otp_code' => 'abcdef',
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['otp_code']);
        });
    });

    describe('resetPassword', function (): void {
        it('can reset password with valid OTP', function (): void {
            $oldPassword = 'oldpassword';

            $user = User::factory()->create([
                'email' => 'john.doe@example.com',
                'password' => Hash::make($oldPassword),
            ]);

            $otpCode = '123456';

            $this->otpCacheService->cacheOtpCodeForUser($user, $otpCode);

            $newPassword = 'newpassword123';

            $response = $this->postJson(route('api.auth.password.otp.reset'), [
                'email' => $user->email,
                'otp_code' => $otpCode,
                'password' => $newPassword,
                'password_confirmation' => $newPassword,
            ]);

            $response->assertStatus(200)->assertJson(['message' => 'Password reset successfully']);

            $user->refresh();

            expect(Hash::check($newPassword, $user->password))->toBeTrue();

            expect(Hash::check($oldPassword, $user->password))->toBeFalse();
        });

        it('fails to reset password with invalid OTP', function (): void {
            $oldPassword = 'oldpassword';

            $user = User::factory()->create([
                'email' => 'john.doe@example.com',
                'password' => Hash::make($oldPassword),
            ]);

            $validOtpCode = '123456';

            $this->otpCacheService->cacheOtpCodeForUser($user, $validOtpCode);

            $invalidOtpCode = '654321';

            $newPassword = 'newpassword123';

            $response = $this->postJson(route('api.auth.password.otp.reset'), [
                'email' => $user->email,
                'otp_code' => $invalidOtpCode,
                'password' => $newPassword,
                'password_confirmation' => $newPassword,
            ]);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['otp_code']);

            $user->refresh();

            expect(Hash::check($oldPassword, $user->password))->toBeTrue();

            expect(Hash::check($newPassword, $user->password))->toBeFalse();
        });
    });
})->group('jwt-auth');
