<?php

namespace Database\Seeders;

use App\Data\City\CreateCityData;
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
        $countriesPath = database_path('seeders/data/countries.json');

        $countriesJsonContent = $this->filesystem->get($countriesPath);

        $countriesData = new Collection(json_decode((string) $countriesJsonContent, true));

        $citiesPath = database_path('seeders/data/cities.json');

        $citiesJsonContent = $this->filesystem->get($citiesPath);

        $citiesData = new Collection(json_decode((string) $citiesJsonContent, true));

        $countriesData = $countriesData->map(function (array $country) use ($citiesData): array {
            $cities = $citiesData->where('country_id', $country['vendor_id'])->all();

            $createCitiesData = CreateCityData::collect($cities, Collection::class);

            $country['cities'] = $createCitiesData->values()->toArray();

            return $country;
        });

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
                    $country->cities()->withoutGlobalScopes()->delete();
                }

                $country->addMediaFromString($countryData->flag)
                    ->usingFileName("flag-{$countryData->iso31661Alpha2}.svg")
                    ->toMediaCollection('flags');

                $countryData->cities->each(function (CreateCityData $cityData) use ($country): void {
                    $country->cities()->create([
                        'name' => $cityData->name,
                        'is_active' => $cityData->isActive,
                    ]);
                });
            });
    }
}
