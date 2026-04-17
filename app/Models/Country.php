<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use App\Models\Scopes\ActiveEntity;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

#[ScopedBy(ActiveEntity::class)]
#[Fillable([
    'iso_3166_1_alpha2',
    'iso_3166_1_alpha3',
    'common_name',
    'official_name',
    'is_active',
])]
class Country extends Model implements HasMedia {
    use HasFactory;
    use HasTranslations;
    use InteractsWithMedia;

    public array $translatable = [
        'common_name',
        'official_name',
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

    #[Scope]
    protected function search(Builder $query, string $search): Builder {
        return $query->where(function (Builder $q) use ($search): void {
            $q->whereJsonContains('common_name->en', $search)
                ->orWhereJsonContains('common_name->ar', $search)
                ->orWhereJsonContains('common_name->fr', $search)
                ->orWhereJsonContains('official_name->en', $search)
                ->orWhereJsonContains('official_name->ar', $search)
                ->orWhereJsonContains('official_name->fr', $search)
                ->orWhereLike('iso_3166_1_alpha2', "%{$search}%")
                ->orWhereLike('iso_3166_1_alpha3', "%{$search}%");
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\City, $this>
     */
    public function cities(): HasMany {
        return $this->hasMany(City::class);
    }
}
