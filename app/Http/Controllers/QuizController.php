<?php

namespace App\Http\Controllers;

use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuizController extends Controller
{
    public function index(): View
    {
        return view('quiz.index', ['questions' => QuizQuestion::all()]);
    }

    public function submit(Request $request): RedirectResponse
    {
        $questions = QuizQuestion::all();
        $answers = $request->input('answers', []);
        $score = $questions->filter(fn ($question) => isset($answers[$question->id]) && (int) $answers[$question->id] === $question->correct_index)->count();
        $attempt = QuizAttempt::create(['user_id' => $request->user()->id, 'score' => $score, 'total' => $questions->count(), 'answers' => $answers]);
        return redirect()->route('quiz.result', $attempt);
    }

    public function result(QuizAttempt $attempt): View
    {
        abort_unless($attempt->user_id === auth()->id(), 403);
        return view('quiz.result', ['attempt' => $attempt, 'questions' => QuizQuestion::all()]);
    }
}
