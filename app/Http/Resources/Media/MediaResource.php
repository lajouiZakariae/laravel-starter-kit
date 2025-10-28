<?php

namespace App\Http\Resources\Media;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        /** @var \Spatie\MediaLibrary\MediaCollections\Models\Media $media */
        $media = $this->resource;

        return [
            'url' => $media->getUrl(),
        ];
    }
}
