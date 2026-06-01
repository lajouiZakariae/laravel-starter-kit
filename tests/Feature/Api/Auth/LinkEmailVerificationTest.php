<?php

use App\Mail\EmailVerification\Link\EmailVerificationLinkMail;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\ApiTestCase;

/**
 * @var ApiTestCase $this
 */
describe('Link Email Verification', function (): void {
    beforeEach(function (): void {
        Mail::fake();
    });

    describe('Send Verification Link', function (): void {
        it('can send a verification link for a valid email', function (): void {
            $user = User::factory()->create(['email_verified_at' => null]);

            $response = $this->post(route('api.auth.email.link.send'), ['email' => $user->email]);

            $response->assertSuccessful()->assertJson(['message' => 'Verification link sent']);

            Mail::assertQueued(EmailVerificationLinkMail::class);

            $this->assertDatabaseHas('email_verification_tokens', ['email' => $user->email]);
        });

        it('replaces an existing token when a new one is requested', function (): void {
            $user = User::factory()->create(['email_verified_at' => null]);

            $this->post(route('api.auth.email.link.send'), ['email' => $user->email]);
            $this->post(route('api.auth.email.link.send'), ['email' => $user->email]);

            $this->assertDatabaseCount('email_verification_tokens', 1);
        });

        it('fails validation when email does not exist', function (): void {
            $response = $this->post(route('api.auth.email.link.send'), ['email' => 'nonexistent@example.com']);

            $response->assertUnprocessable();
        });

        it('respects rate limiting for sending verification links', function (): void {
            $user = User::factory()->create(['email_verified_at' => null]);

            Collection::times(4, function (int $index) use ($user): void {
                $response = $this->post(route('api.auth.email.link.send'), ['email' => $user->email]);

                ($index < 4) ? $response->assertSuccessful() : $response->assertStatus(429);
            });
        });
    });

    describe('Verify Email via Link', function (): void {
        it('can verify email with a valid token', function (): void {
            $user = User::factory()->create(['email_verified_at' => null]);

            $plainToken = Str::random(64);

            DB::table('email_verification_tokens')->insert([
                'email' => $user->email,
                'token' => hash('sha256', $plainToken),
                'created_at' => now(),
            ]);

            $response = $this->post(route('api.auth.email.link.verify'), [
                'email' => $user->email,
                'token' => $plainToken,
            ]);

            $response->assertSuccessful()->assertJson(['message' => 'Email verified successfully']);

            $user->refresh();
            expect($user->hasVerifiedEmail())->toBeTrue();

            $this->assertDatabaseMissing('email_verification_tokens', ['email' => $user->email]);
        });

        it('fails with an invalid token', function (): void {
            $user = User::factory()->create(['email_verified_at' => null]);

            DB::table('email_verification_tokens')->insert([
                'email' => $user->email,
                'token' => hash('sha256', Str::random(64)),
                'created_at' => now(),
            ]);

            $response = $this->post(route('api.auth.email.link.verify'), [
                'email' => $user->email,
                'token' => 'wrong-token',
            ]);

            $response->assertBadRequest();
        });

        it('fails with an expired token', function (): void {
            $user = User::factory()->create(['email_verified_at' => null]);

            $plainToken = Str::random(64);

            DB::table('email_verification_tokens')->insert([
                'email' => $user->email,
                'token' => hash('sha256', $plainToken),
                'created_at' => now()->subMinutes(61),
            ]);

            $response = $this->post(route('api.auth.email.link.verify'), [
                'email' => $user->email,
                'token' => $plainToken,
            ]);

            $response->assertBadRequest();
        });

        it('fails when no token record exists for the email', function (): void {
            $user = User::factory()->create(['email_verified_at' => null]);

            $response = $this->post(route('api.auth.email.link.verify'), [
                'email' => $user->email,
                'token' => Str::random(64),
            ]);

            $response->assertBadRequest();
        });
    });
})->group('link-email-verification');
