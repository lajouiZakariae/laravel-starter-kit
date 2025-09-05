<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Full Authentication Flow', function (): void {
    it('can complete basic authentication flow', function (): void {
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

        $profileResponse = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->getJson('/api/auth/me');

        $profileResponse->assertStatus(200);

        expect($profileResponse->json('data.email'))->toBe('jane.smith@example.com');
    });
})->group('jwt-auth');
