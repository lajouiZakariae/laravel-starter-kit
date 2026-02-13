<?php

/**
 * @var \Tests\ApiTestCase $this
 */
describe('Full Authentication Flow', function (): void {
    it('can complete basic authentication flow', function (): void {
        $userData = [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone_number_country_code' => 'MA',
            'phone_number' => '+212689898989',
        ];

        $registerResponse = $this->post(route('api.auth.register'), $userData);

        $registerResponse->assertStatus(201);

        $token = $registerResponse->json('meta.token');

        $profileResponse = $this->withHeaders([
            'Authorization' => "Bearer {$token}",
        ])->getJson(route('api.auth.me'));

        $profileResponse->assertStatus(200);

        expect($profileResponse->json('data.email'))->toBe('jane.smith@example.com');
    });
})->group('jwt-auth');
