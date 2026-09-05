<?php

namespace App\Http\Controllers;

use App\Models\CaseReflection;
use App\Models\DiagnosticAssessment;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DiagnosisController extends Controller
{
    public function show(Request $request): View
    {
        $assessments = DiagnosticAssessment::where('user_id', $request->user()->id)
            ->get()->keyBy('phase');
        $pre = $assessments->get('pre');
        $post = $assessments->get('post');
        $hasLearningAfterPre = $pre && CaseReflection::where('user_id', $request->user()->id)
            ->where('created_at', '>=', $pre->created_at)
            ->exists();

        return view('diagnosis.show', [
            'items' => config('diagnosis.items'),
            'choices' => config('diagnosis.choices'),
            'pre' => $pre,
            'post' => $post,
            'phase' => $pre ? 'post' : 'pre',
            'canTakePost' => $hasLearningAfterPre,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var array<string, string> $items */
        $items = config('diagnosis.items');
        $validated = $request->validate([
            'phase' => ['required', Rule::in(['pre', 'post'])],
            'responses' => ['required', 'array'],
            'responses.*' => ['required', 'integer', 'between:1,5'],
        ]);
        abort_unless(array_keys($validated['responses']) === array_keys($items), 422);

        $userId = $request->user()->id;
        if ($validated['phase'] === 'pre') {
            abort_if(DiagnosticAssessment::where('user_id', $userId)->exists(), 403);
        } else {
            $pre = DiagnosticAssessment::where('user_id', $userId)->where('phase', 'pre')->firstOrFail();
            abort_unless(CaseReflection::where('user_id', $userId)->where('created_at', '>=', $pre->created_at)->exists(), 403);
        }

        try {
            DiagnosticAssessment::create([
                'user_id' => $userId,
                'phase' => $validated['phase'],
                'responses' => $validated['responses'],
            ]);
        } catch (UniqueConstraintViolationException) {
            return redirect()->route('diagnosis.show')->withErrors(['diagnosis' => 'この診断はすでに記録済みです。']);
        }

        return redirect()->route('diagnosis.show')->with('status', '対話の振り返りを保存しました。');
    }
}
