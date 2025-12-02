<?php

namespace App\Models;

use App\Models\Scopes\ActiveEntity;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy(ActiveEntity::class)]
class City extends Model {
    protected $fillable = [
        'country_id',
        'name',
        'is_active',
    ];

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
