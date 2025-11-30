<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     */
    public function run(): void {
        // $this->call([
        //     CountrySeeder::class,
        // ]);

        // User::factory(60)->create();

        $content = File::json(storage_path('data/countries.json'));

        $collection = collect($content);

        File::put(storage_path('data/names-countries.json'), $collection
            ->filter(fn ($item): bool => $item['common_name']['ar'] === null)
            ->pluck('common_name')
            ->toPrettyJson());

        //     $content = File::json(storage_path('data/countries.json'));
        // $fixedNames = collect(File::json(storage_path('data/fixed.json')));

        // $content = collect($content)->map(function ($item) use ($fixedNames) {
        //     $fixedName = $fixedNames->firstWhere('en', $item['common_name']['en']);

        //     if ($fixedName) {
        //         $item['common_name']['ar'] = $fixedName['ar'];
        //     }

        //     return $item;
        // });

        // File::put(storage_path('data/fixed-countries.json'), $content->toJson());
    }
}
