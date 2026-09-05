<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoleplaySession extends Model
{
    protected $fillable = [
        'user_id', 'persona_id', 'scenario_id', 'mode', 'status', 'room_code',
        'mentor_name', 'mentee_name', 'observer_name', 'score', 'reflection', 'completed_at',
    ];

    protected function casts(): array
    {
        return ['completed_at' => 'datetime'];
    }

    /** @return BelongsTo<Persona, $this> */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    /** @return BelongsTo<Scenario, $this> */
    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class);
    }

    /** @return HasMany<RoleplayMessage, $this> */
    public function messages(): HasMany
    {
        return $this->hasMany(RoleplayMessage::class);
    }

    /** @return HasMany<RoleplayFeedback, $this> */
    public function feedback(): HasMany
    {
        return $this->hasMany(RoleplayFeedback::class);
    }
}
