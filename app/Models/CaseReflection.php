<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaseReflection extends Model
{
    protected $fillable = [
        'user_id',
        'scenario_id',
        'selected_response_id',
        'selected_response_content',
        'selection_reason',
        'difference',
        'next_action',
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Scenario, $this> */
    public function scenario(): BelongsTo
    {
        return $this->belongsTo(Scenario::class);
    }

    /** @return BelongsTo<DrillResponse, $this> */
    public function selectedResponse(): BelongsTo
    {
        return $this->belongsTo(DrillResponse::class, 'selected_response_id');
    }
}
