<?php

namespace Database\Seeders;

use App\Data\Country\CreateCountryData;
use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;

class CountrySeeder extends Seeder {
    public function __construct(private readonly Filesystem $filesystem) {}

    /**
     * Run the database seeds.
     */
    public function run(): void {
        $countriesJsonContent = $this->filesystem->get(storage_path('data/countries.json'));

        $countriesData = new Collection(json_decode((string) $countriesJsonContent, true));

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
