<?php

namespace App\Http\Controllers;

use App\Models\CaseReflection;
use App\Models\QuizAttempt;
use App\Models\RoleplaySession;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $userId = auth()->id();

        return view('dashboard', [
            'soloCount' => RoleplaySession::where('user_id', $userId)->where('mode', 'solo')->where('status', 'completed')->count(),
            'trioCount' => RoleplaySession::where('user_id', $userId)->where('mode', 'trio')->where('status', 'completed')->count(),
            'quizBest' => QuizAttempt::where('user_id', $userId)->max('score'),
            'recentSessions' => RoleplaySession::with('scenario')->where('user_id', $userId)->latest()->take(5)->get(),
            'caseReflectionCount' => CaseReflection::where('user_id', $userId)->count(),
            'recentCaseReflections' => CaseReflection::with('scenario')
                ->where('user_id', $userId)
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
