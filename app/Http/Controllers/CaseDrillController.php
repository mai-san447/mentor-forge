<?php

namespace App\Http\Controllers;

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

        // 他者回答は、自分が回答した後に限って取得する。ビューで隠すだけにしない。
        $responses = $ownResponse
            ? $scenario->drillResponses()->oldest()->get(['id', 'content', 'created_at'])
            : collect();

        return view('cases.show', compact('scenario', 'ownResponse', 'responses'));
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
}
