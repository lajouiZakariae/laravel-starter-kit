<?php

namespace App\Http\Resources\Country;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        /** @var \App\Models\Country $countryInstance */
        $countryInstance = $this->resource;

        return [
            'id' => $this->whenHas('id'),
            'iso_3166_1_alpha2' => $this->whenHas('iso_3166_1_alpha2'),
            'common_name' => $this->whenHas('common_name'),
            'created_at' => $this->whenHas('created_at'),
            'updated_at' => $this->whenHas('updated_at'),
        ];
    }
}
