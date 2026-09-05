<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleplayMessage extends Model
{
    protected $fillable = ['roleplay_session_id', 'speaker', 'content'];

    /** @return BelongsTo<RoleplaySession, $this> */
    public function session(): BelongsTo
    {
        return $this->belongsTo(RoleplaySession::class, 'roleplay_session_id');
    }
}
