<?php

namespace App\Http\Controllers;

use App\Models\RoleplaySession;
use App\Models\Scenario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TrioPracticeController extends Controller
{
    public function index(): View
    {
        return view('trio.index', ['scenarios' => Scenario::with('persona')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'scenario_id' => ['required', 'exists:scenarios,id'],
            'mentor_name' => ['required', 'string', 'max:50'],
            'mentee_name' => ['required', 'string', 'max:50'],
            'observer_name' => ['required', 'string', 'max:50'],
        ]);
        $scenario = Scenario::findOrFail($validated['scenario_id']);
        $session = RoleplaySession::create(array_merge($validated, ['user_id' => $request->user()->id, 'persona_id' => $scenario->persona_id, 'mode' => 'trio', 'room_code' => strtoupper(Str::random(6))]));
        return redirect()->route('trio.show', $session);
    }

    public function join(Request $request): RedirectResponse
    {
        $validated = $request->validate(['room_code' => ['required', 'string', 'size:6']]);
        $session = RoleplaySession::where('room_code', strtoupper($validated['room_code']))->where('mode', 'trio')->first();
        return $session ? redirect()->route('trio.show', $session) : back()->withErrors(['room_code' => 'ルームが見つかりません。コードを確認してください。']);
    }

    public function show(RoleplaySession $session): View
    {
        abort_unless($session->mode === 'trio', 404);
        return view('trio.show', ['session' => $session->load(['persona', 'scenario', 'feedback'])]);
    }

    public function feedback(Request $request, RoleplaySession $session): RedirectResponse
    {
        abort_unless($session->mode === 'trio', 404);
        $validated = $request->validate([
            'reviewer_role' => ['required', 'in:observer,mentee,self'],
            'listening_score' => ['required', 'integer', 'between:1,5'],
            'empathy_score' => ['required', 'integer', 'between:1,5'],
            'question_score' => ['required', 'integer', 'between:1,5'],
            'strengths' => ['required', 'string', 'max:1000'],
            'improvements' => ['required', 'string', 'max:1000'],
        ]);
        $session->feedback()->create($validated);
        $average = $session->feedback()->selectRaw('AVG((listening_score + empathy_score + question_score) / 3) AS average')->value('average');
        $session->update(['score' => (int) round($average * 20), 'status' => 'completed', 'completed_at' => now()]);
        return back()->with('status', 'フィードバックを保存しました。');
    }
}
