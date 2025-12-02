<?php

namespace App\Data\Country;

use App\Data\city\CreateCityData;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class CreateCountryData extends Data {
    public function __construct(
        public TranslatedPropertyData $commonName,
        public TranslatedPropertyData $officialName,
        #[MapName('iso_3166_1_alpha2')]
        public string $iso31661Alpha2,
        #[MapName('iso_3166_1_alpha3')]
        public string $iso31661Alpha3,
        public string $flag,
        public bool $isActive,
        /** @var Collection<int,CreateCityData> */
        public Collection $cities,
    ) {}
}
