<?php

use App\Models\User;
use App\Services\EmailVerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @var \Tests\TestCase $this
 */
uses(RefreshDatabase::class);

describe('Email Verification', function (): void {
    beforeEach(function (): void {
        $this->emailVerificationService = $this->mock(EmailVerificationService::class);
    });

    describe('Send Verification Email', function (): void {
        it('can send verification email for unauthenticated user', function (): void {
            $user = User::factory()->create([
                'email' => 'john.doe@example.com',
                'email_verified_at' => null,
            ]);

            $this->emailVerificationService
                ->shouldReceive('sendVerificationEmail')
                ->once()
                ->with(\Mockery::on(fn ($u) => $u->email === $user->email));

            $response = $this->post(route('api.auth.email.send'), [
                'email' => 'john.doe@example.com',
            ]);

            $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Verification email sent',
                ]);
        });

        it('can send verification email for authenticated user', function (): void {
            $user = User::factory()->create([
                'email' => 'john.doe@example.com',
                'email_verified_at' => null,
            ]);

            $this->actingAs($user, 'api');

            $this->emailVerificationService
                ->shouldReceive('sendVerificationEmail')
                ->once()
                ->with(\Mockery::on(fn ($u) => $u->id === $user->id));

            $response = $this->post(route('api.auth.email.send'));

            $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Verification email sent',
                ]);
        });

        it('fails to send verification email if user not found', function (): void {
            $response = $this->post(route('api.auth.email.send'), [
                'email' => 'nonexistent@example.com',
            ]);

            $response->assertStatus(422)->assertJsonValidationErrors([
                'email',
            ]);
        });
    });
})->group('email-verification');
