<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleplayFeedback extends Model
{
    protected $fillable = [
        'roleplay_session_id', 'reviewer_role', 'listening_score', 'empathy_score',
        'question_score', 'strengths', 'improvements',
    ];

    /** @return BelongsTo<RoleplaySession, $this> */
    public function session(): BelongsTo
    {
        return $this->belongsTo(RoleplaySession::class, 'roleplay_session_id');
    }
}
