<?php

namespace App\Models;

use App\Models\Scopes\ActiveCountry;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

#[ScopedBy(ActiveCountry::class)]
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

    public function scopeSearch(Builder $query, string $search) {
        return $query->where(function (Builder $q) use ($search) {
            $q->whereJsonContains('common_name->en', $search)
                ->orWhereJsonContains('common_name->ar', $search)
                ->orWhereJsonContains('common_name->fr', $search)
                ->orWhereJsonContains('official_name->en', $search)
                ->orWhereJsonContains('official_name->ar', $search)
                ->orWhereJsonContains('official_name->fr', $search)
                ->orWhere('iso_3166_1_alpha2', 'like', "%{$search}%")
                ->orWhere('iso_3166_1_alpha3', 'like', "%{$search}%");
        });
    }
}
