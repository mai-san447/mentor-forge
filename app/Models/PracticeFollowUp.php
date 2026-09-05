<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PracticeFollowUp extends Model
{
    protected $fillable = [
        'user_id', 'case_reflection_id', 'weeks_after', 'practiced',
        'counterpart_reaction', 'consultation_change', 'note',
    ];

    protected function casts(): array
    {
        return ['practiced' => 'boolean'];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<CaseReflection, $this> */
    public function caseReflection(): BelongsTo
    {
        return $this->belongsTo(CaseReflection::class);
    }
}
