<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * @var \Tests\ApiTestCase $this
 */
describe('Login', function (): void {
    it('can login with valid credentials', function (): void {
        $user = User::factory()->create([
            'email' => 'john.doe@example.com',
            'password' => Hash::make('password123'),
        ]);

        $credentials = [
            'email' => 'john.doe@example.com',
            'password' => 'password123',
        ];

        $response = $this->post(route('api.auth.login'), $credentials);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'first_name',
                    'last_name',
                    'phone_number',
                    'email',
                    'email_verified_at',
                    'created_at',
                    'updated_at',
                ],
                'meta' => [
                    'token',
                    'token_type',
                ],
            ]);

        expect($response->json('data.id'))->toBe($user->id);
        expect($response->json('meta.token'))->not->toBeEmpty();
        expect($response->json('meta.token_type'))->toBe('Bearer');
    });

    it('fails login with invalid email', function (): void {
        $credentials = [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ];

        $response = $this->post(route('api.auth.login'), $credentials);

        $response->assertStatus(422);
    });

    it('fails login with invalid password', function (): void {
        User::factory()->create([
            'email' => 'john.doe@example.com',
            'password' => Hash::make('password123'),
        ]);

        $credentials = [
            'email' => 'john.doe@example.com',
            'password' => 'wrongpassword',
        ];

        $response = $this->post(route('api.auth.login'), $credentials);

        $response->assertStatus(422);
    });

    it('validates required fields for login', function (): void {
        $response = $this->post(route('api.auth.login'), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    });

    it('validates email format for login', function (): void {
        $credentials = [
            'email' => 'invalid-email',
            'password' => 'password123',
        ];

        $response = $this->post(route('api.auth.login'), $credentials);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });
})->group('jwt-auth');
