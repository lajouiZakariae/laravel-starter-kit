<?php

namespace Tests;

use Database\Seeders\CountrySeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class ApiTestCase extends BaseTestCase {
    protected function setUp(): void {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json',
        ]);

        $this->seed(CountrySeeder::class);
    }
}
