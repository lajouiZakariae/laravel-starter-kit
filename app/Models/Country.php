<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Country extends Model implements HasMedia {
    use HasTranslations;
    use InteractsWithMedia;

    public array $translatable = [
        'common_name',
        'official_name',
    ];

    protected $fillable = [
        'iso_3166_1_alpha2',
        'iso_3166_1_alpha3',
        'common_name',
        'official_name',
        'is_active',
    ];

    protected function casts(): array {
        return [
            'common_name' => 'array',
            'official_name' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection('flags')
            ->singleFile();
    }
}
