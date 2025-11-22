<?php

use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Uri;

/**
 * @var \Tests\TestCase $this
 */
describe('Password Reset', function (): void {
    beforeEach(function (): void {
        Mail::fake();
    });

    test('reset password link can be requested', function (): void {
        $user = User::factory()->create();

        $this->post(route('api.auth.password.send-reset'), ['email' => $user->email]);

        Mail::assertSent(PasswordResetMail::class);
    });

    test('password can be reset with valid token', function (): void {
        $user = User::factory()->create();

        $this->post(route('api.auth.password.send-reset'), ['email' => $user->email]);

        Mail::assertSent(PasswordResetMail::class, function ($notification) use ($user): bool {
            $url = $notification->url;

            $token = Uri::of($url)->query()->get('token');

            $response = $this->post(route('api.auth.password.reset'), [
                'email' => $user->email,
                'token' => $token,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertStatus(200);

            return true;
        });
    });
})->group('jwt-auth');
