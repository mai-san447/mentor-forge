<?php

namespace App\Http\Controllers;

use App\Models\CaseReflection;
use App\Models\PracticeFollowUp;
use App\Support\FollowUpSchedule;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PracticeFollowUpController extends Controller
{
    public function show(Request $request, CaseReflection $reflection, int $weeks): View
    {
        $this->authorizeRequest($request, $reflection, $weeks);
        $dueAt = FollowUpSchedule::dueAt($reflection, $request->user(), $weeks);

        return view('follow-ups.show', [
            'reflection' => $reflection->load('scenario'),
            'weeks' => $weeks,
            'dueAt' => $dueAt,
            'isDue' => now()->startOfDay()->greaterThanOrEqualTo($dueAt),
            'followUp' => $reflection->followUps()->where('weeks_after', $weeks)->first(),
            'isPilotTester' => FollowUpSchedule::isPilotTester($request->user()),
        ]);
    }

    public function store(Request $request, CaseReflection $reflection, int $weeks): RedirectResponse
    {
        $this->authorizeRequest($request, $reflection, $weeks);
        $dueAt = FollowUpSchedule::dueAt($reflection, $request->user(), $weeks);
        abort_unless(now()->startOfDay()->greaterThanOrEqualTo($dueAt), 403);

        $validated = $request->validate([
            'practiced' => ['required', 'boolean'],
            'counterpart_reaction' => [Rule::requiredIf($request->boolean('practiced')), 'nullable', 'string', 'max:2000'],
            'consultation_change' => [Rule::requiredIf($request->boolean('practiced')), 'nullable', Rule::in(['deeper', 'same', 'shallower', 'unknown'])],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            PracticeFollowUp::create([
                'user_id' => $request->user()->id,
                'case_reflection_id' => $reflection->id,
                'weeks_after' => $weeks,
                'practiced' => $validated['practiced'],
                'counterpart_reaction' => $validated['counterpart_reaction'] ?? null,
                'consultation_change' => $validated['consultation_change'] ?? null,
                'note' => $validated['note'] ?? null,
            ]);
        } catch (UniqueConstraintViolationException) {
            return back()->withErrors(['follow_up' => 'この時期の記録はすでに保存済みです。']);
        }

        return redirect()->route('dashboard')->with('status', $weeks.'週間後の実践記録を保存しました。');
    }

    private function authorizeRequest(Request $request, CaseReflection $reflection, int $weeks): void
    {
        abort_unless($reflection->user_id === $request->user()->id, 403);
        abort_unless(in_array($weeks, [2, 4], true), 404);
    }
}
