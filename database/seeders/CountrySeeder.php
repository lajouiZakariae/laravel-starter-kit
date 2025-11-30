<?php

namespace Database\Seeders;

use App\Data\Country\CreateCountryData;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CountrySeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $countriesJsonContent = File::get(storage_path('data/countries.json'));

        $countriesData = collect(json_decode($countriesJsonContent, true));

        $countriesData
            ->map(fn (array $countryPayload): CreateCountryData => CreateCountryData::from($countryPayload))
            ->each(function (CreateCountryData $countryData): void {
                $country = Country::query()->withoutGlobalScopes()->updateOrCreate(
                    ['iso_3166_1_alpha2' => $countryData->iso31661Alpha2],
                    [
                        'iso_3166_1_alpha3' => $countryData->iso31661Alpha3,
                        'common_name' => ($countryData->commonName->toArray()),
                        'official_name' => $countryData->officialName->toArray(),
                        'is_active' => $countryData->isActive,
                    ]
                );

                if (! $country->wasRecentlyCreated) {
                    $country->deleteAllMedia();
                }

                $country->addMediaFromString($countryData->flag)
                    ->usingFileName("flag-{$countryData->iso31661Alpha2}.svg")
                    ->toMediaCollection('flags');
            });
    }
}
