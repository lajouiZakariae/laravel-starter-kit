<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Filesystem\Filesystem;

class DatabaseSeeder extends Seeder {
    public function __construct(private readonly Filesystem $filesystem) {}

    /**
     * Seed the application's database.
     */
    public function run(): void {
        $this->call([
            CountrySeeder::class,
        ]);

        User::factory(5)->create();
    }
}
