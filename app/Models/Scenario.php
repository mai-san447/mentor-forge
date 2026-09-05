<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Scenario extends Model
{
    protected $fillable = ['persona_id', 'title', 'situation', 'goal', 'difficulty'];

    /** @return BelongsTo<Persona, $this> */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    /** @return HasMany<DrillResponse, $this> */
    public function drillResponses(): HasMany
    {
        return $this->hasMany(DrillResponse::class);
    }
}
