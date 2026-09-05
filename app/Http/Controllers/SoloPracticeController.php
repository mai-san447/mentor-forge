<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\RoleplaySession;
use App\Models\Scenario;
use App\Services\RoleplayReplyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SoloPracticeController extends Controller
{
    public function index(): View
    {
        return view('solo.index', ['personas' => Persona::with('scenarios')->get()]);
    }

    public function start(Request $request, Scenario $scenario): RedirectResponse
    {
        $session = RoleplaySession::create(['user_id' => $request->user()->id, 'persona_id' => $scenario->persona_id, 'scenario_id' => $scenario->id, 'mode' => 'solo']);
        $session->messages()->create(['speaker' => 'persona', 'content' => '今日は時間を取っていただいてありがとうございます。正直、何から話せばいいのか少し迷っています。']);

        return redirect()->route('solo.show', $session);
    }

    public function show(RoleplaySession $session): View
    {
        $this->authorizeSession($session);

        return view('solo.show', ['session' => $session->load(['persona', 'scenario', 'messages'])]);
    }

    public function message(Request $request, RoleplaySession $session): RedirectResponse
    {
        $this->authorizeSession($session);
        $validated = $request->validate(['message' => ['required', 'string', 'max:1000']]);
        $session->messages()->create(['speaker' => 'mentor', 'content' => $validated['message']]);
        $session->messages()->create(['speaker' => 'persona', 'content' => $this->personaReply($validated['message'], $session)]);

        return back();
    }

    public function complete(Request $request, RoleplaySession $session): RedirectResponse
    {
        $this->authorizeSession($session);
        $validated = $request->validate(['reflection' => ['nullable', 'string', 'max:2000']]);
        $messages = $session->messages()->where('speaker', 'mentor')->pluck('content');
        $score = min(100, 45 + ($messages->count() * 8) + ($messages->filter(fn ($message) => str_contains($message, '？') || str_contains($message, '?'))->count() * 5));
        $session->update(['status' => 'completed', 'score' => $score, 'reflection' => $validated['reflection'] ?? null, 'completed_at' => now()]);

        return redirect()->route('solo.result', $session);
    }

    public function result(RoleplaySession $session): View
    {
        $this->authorizeSession($session);

        return view('solo.result', ['session' => $session->load(['persona', 'scenario', 'messages'])]);
    }

    private function authorizeSession(RoleplaySession $session): void
    {
        abort_unless($session->user_id === auth()->id() && $session->mode === 'solo', 403);
    }

    private function personaReply(string $message, RoleplaySession $session): string
    {
        $persona = $session->persona;
        $scenario = $session->scenario;
        abort_unless($persona !== null && $scenario !== null, 404);

        return app(RoleplayReplyService::class)->reply(
            $persona,
            $scenario->title,
            $session->messages()->get(['speaker', 'content'])->toArray(),
        );
    }
}
