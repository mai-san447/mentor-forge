<?php

namespace App\Support;

use App\Models\CaseReflection;
use App\Models\User;
use Illuminate\Support\Carbon;

class FollowUpSchedule
{
    public static function dueAt(CaseReflection $reflection, User $user, int $weeks): Carbon
    {
        if (self::isPilotTester($user)) {
            return $reflection->created_at->copy()->startOfDay();
        }

        return $reflection->created_at->copy()->addWeeks($weeks)->startOfDay();
    }

    public static function isPilotTester(User $user): bool
    {
        return config('pilot.enabled') === true
            && is_string(config('pilot.tester_email'))
            && mb_strtolower($user->email) === mb_strtolower(config('pilot.tester_email'));
    }
}
