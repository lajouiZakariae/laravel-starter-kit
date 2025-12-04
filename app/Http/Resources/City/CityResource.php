<?php

namespace App\Http\Resources\City;

use App\Http\Resources\Country\CountryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id' => $this->whenHas('id'),
            'country_id' => $this->whenHas('country_id'),
            'name' => $this->whenHas('name'),
            'is_active' => $this->whenHas('is_active'),
            'created_at' => $this->whenHas('created_at'),
            'updated_at' => $this->whenHas('updated_at'),

            // Relationships
            'country' => new CountryResource($this->whenLoaded('country')),
        ];
    }
}
