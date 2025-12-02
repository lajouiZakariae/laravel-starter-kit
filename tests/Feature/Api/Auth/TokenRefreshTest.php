<?php

/**
 * @var \Tests\ApiTestCase $this
 */
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
})->group('jwt-auth');
