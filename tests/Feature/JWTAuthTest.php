<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

describe('JWT Authentication', function (): void {
    describe('Registration', function (): void {
        it('can register a new user successfully', function (): void {
            $userData = [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ];

            $response = $this->post(route('api.auth.register'), $userData);

            $response->assertStatus(201)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'first_name',
                        'last_name',
                        'email',
                        'email_verified_at',
                        'created_at',
                        'updated_at',
                    ],
                    'meta' => [
                        'token',
                        'token_type',
                        'expires_in',
                    ],
                ]);

            $this->assertDatabaseHas('users', [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
            ]);

            expect($response->json('meta.token'))->not->toBeEmpty();
            expect($response->json('meta.token_type'))->toBe('Bearer');
        });

        it('validates required fields for registration', function (): void {
            $response = $this->post(route('api.auth.register'), []);

            $response->assertStatus(422)
                ->assertJsonValidationErrors([
                    'first_name',
                    'last_name',
                    'email',
                    'password',
                ]);
        });

        it('validates email format', function (): void {
            $userData = [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'invalid-email',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ];

            $response = $this->post(route('api.auth.register'), $userData);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
        });

        it('validates password confirmation', function (): void {
            $userData = [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'password' => 'password123',
                'password_confirmation' => 'differentpassword',
            ];

            $response = $this->post(route('api.auth.register'), $userData);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
        });

        it('validates password minimum length', function (): void {
            $userData = [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'password' => 'short',
                'password_confirmation' => 'short',
            ];

            $response = $this->post(route('api.auth.register'), $userData);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
        });

        it('prevents duplicate email registration', function (): void {
            User::factory()->create(['email' => 'john.doe@example.com']);

            $userData = [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ];

            $response = $this->post(route('api.auth.register'), $userData);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
        });

        it('validates string fields are not too long', function (): void {
            $userData = [
                'first_name' => str_repeat('a', 256),
                'last_name' => str_repeat('b', 256),
                'email' => 'john.doe@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ];

            $response = $this->post(route('api.auth.register'), $userData);

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['first_name', 'last_name']);
        });
    });

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
    });

    describe('Token Refresh', function (): void {
        it('requires authentication to refresh token', function (): void {
            $response = $this->post(route('api.auth.refresh'));

            $response->assertStatus(401);
        });

        it('fails with invalid token for refresh', function (): void {
            $response = $this->withHeaders([
                'Authorization' => 'Bearer invalid-token',
            ])->post(route('api.auth.refresh'));

            $response->assertStatus(401);
        });
    });

    describe('Full Authentication Flow', function (): void {
        it('can complete basic authentication flow', function (): void {
            // 1. Register a new user
            $userData = [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ];

            $registerResponse = $this->post(route('api.auth.register'), $userData);
            $registerResponse->assertStatus(201);

            $token = $registerResponse->json('meta.token');

            // 2. Get user profile
            $profileResponse = $this->withHeaders([
                'Authorization' => "Bearer {$token}",
            ])->getJson('/api/auth/me');

            $profileResponse->assertStatus(200);
            expect($profileResponse->json('data.email'))->toBe('jane.smith@example.com');
        });
    });
});
