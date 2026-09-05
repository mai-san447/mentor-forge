<?php

namespace App\Http\Controllers;

use App\Models\CaseReflection;
use App\Models\DrillResponse;
use App\Models\Scenario;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CaseDrillController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        return view('cases.index', [
            'scenarios' => Scenario::with('persona')
                ->withExists(['drillResponses as answered' => fn ($query) => $query->where('user_id', $userId)])
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function show(Scenario $scenario): View
    {
        $ownResponse = $scenario->drillResponses()
            ->where('user_id', auth()->id())
            ->first();

        // 他者回答は、自分が回答した後に限って取得する。個人情報は取得せず、最大6件をランダム表示する。
        $otherResponses = $ownResponse
            ? $scenario->drillResponses()
                ->where('user_id', '!=', auth()->id())
                ->inRandomOrder()
                ->limit(6)
                ->get(['id', 'content'])
            : collect();

        $reflection = $scenario->caseReflections()
            ->where('user_id', auth()->id())
            ->first();

        return view('cases.show', compact('scenario', 'ownResponse', 'otherResponses', 'reflection'));
    }

    public function store(Request $request, Scenario $scenario): RedirectResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        try {
            DrillResponse::create([
                'user_id' => $request->user()->id,
                'scenario_id' => $scenario->id,
                'content' => $validated['content'],
            ]);
        } catch (UniqueConstraintViolationException) {
            return redirect()->route('cases.show', $scenario)
                ->withErrors(['content' => 'このケースにはすでに回答済みです。']);
        }

        return redirect()->route('cases.show', $scenario)
            ->with('status', '回答を投稿しました。ほかの人の回答を匿名で見られます。');
    }

    public function reflect(Request $request, Scenario $scenario): RedirectResponse
    {
        $validated = $request->validate([
            'selected_response_id' => ['required', 'integer'],
            'selection_reason' => ['required', 'string', 'max:2000'],
            'difference' => ['required', 'string', 'max:2000'],
            'next_action' => ['required', 'string', 'max:1000'],
        ]);

        $ownResponse = $scenario->drillResponses()
            ->where('user_id', $request->user()->id)
            ->first();
        abort_unless($ownResponse !== null, 403);

        $selectedResponse = $scenario->drillResponses()
            ->whereKey($validated['selected_response_id'])
            ->where('user_id', '!=', $request->user()->id)
            ->firstOrFail();

        try {
            CaseReflection::create([
                'user_id' => $request->user()->id,
                'scenario_id' => $scenario->id,
                'selected_response_id' => $selectedResponse->id,
                'selected_response_content' => $selectedResponse->content,
                'selection_reason' => $validated['selection_reason'],
                'difference' => $validated['difference'],
                'next_action' => $validated['next_action'],
            ]);
        } catch (UniqueConstraintViolationException) {
            return redirect()->route('cases.show', $scenario)
                ->withErrors(['reflection' => 'このケースの振り返りはすでに保存済みです。']);
        }

        return redirect()->route('cases.show', $scenario)
            ->with('status', '振り返りと次に試す行動を保存しました。');
    }
}
