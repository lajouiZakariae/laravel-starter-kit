<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use App\Models\Scopes\ActiveEntity;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy(ActiveEntity::class)]
#[Fillable([
    'country_id',
    'name',
    'is_active',
])]
class City extends Model {
    use HasFactory;

    protected function casts(): array {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\Country, $this>
     */
    public function country(): BelongsTo {
        return $this->belongsTo(Country::class);
    }
}
