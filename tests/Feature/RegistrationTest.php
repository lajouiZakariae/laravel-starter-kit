<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Registration', function (): void {
    it('can register a new user successfully', function (): void {
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone_number_country_code' => 'MA',
            'phone_number' => '+212613080111',
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
                    'phone_number',
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
})->group('jwt-auth');
