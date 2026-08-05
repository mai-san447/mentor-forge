<?php

namespace App\Http\Controllers;

use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * 1問ずつ出題し、答えた直後に正誤と解説を見せる。
 * まとめて解答して最後に採点する形式より、その場で学べるほうが続けやすいため。
 *
 * 進行状況はセッションに置く。設問と正解はサーバー側にとどめ、
 * 回答するまで正解が画面のソースに出ないようにしている。
 */
class QuizController extends Controller
{
    private const ANSWERS = 'quiz.answers';

    private const REVEALED = 'quiz.revealed';

    public function index(Request $request): View|RedirectResponse
    {
        $questions = $this->questions();

        if ($questions->isEmpty()) {
            return view('quiz.index', ['question' => null, 'total' => 0]);
        }

        $answers = $request->session()->get(self::ANSWERS, []);
        $revealedId = $request->session()->get(self::REVEALED);

        // 直前に答えた設問があれば、それを解説つきで見せる。
        // なければ、まだ答えていない先頭の設問を出す。
        $question = $revealedId
            ? $questions->firstWhere('id', $revealedId)
            : $questions->get(count($answers));

        // 全問答え終えた状態でここに来るのは、結果画面から戻るなど通常の導線ではない。
        // 記録は next() で作るので、ここでは作らずに最初からやり直させる。
        if (! $question) {
            $request->session()->forget([self::ANSWERS, self::REVEALED]);

            return redirect()->route('quiz.index');
        }

        return view('quiz.index', [
            'question' => $question,
            'number' => $questions->search(fn (QuizQuestion $q) => $q->is($question)) + 1,
            'total' => $questions->count(),
            'selected' => $revealedId ? ($answers[$question->id] ?? null) : null,
            'isLast' => count($answers) >= $questions->count(),
        ]);
    }

    public function answer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'question_id' => ['required', 'integer', 'exists:quiz_questions,id'],
            'choice' => ['required', 'integer', 'min:0'],
        ]);

        $answers = $request->session()->get(self::ANSWERS, []);

        // 同じ設問を二度答えても最初の回答を上書きしない（戻る操作での取り直しを防ぐ）
        if (! array_key_exists($validated['question_id'], $answers)) {
            $answers[$validated['question_id']] = $validated['choice'];
            $request->session()->put(self::ANSWERS, $answers);
        }

        $request->session()->put(self::REVEALED, $validated['question_id']);

        return redirect()->route('quiz.index');
    }

    public function next(Request $request): RedirectResponse
    {
        $request->session()->forget(self::REVEALED);

        $questions = $this->questions();
        $answered = count($request->session()->get(self::ANSWERS, []));

        // 最後の設問を終えたら、この POST のなかで記録まで済ませる。
        // 表示のための GET で記録を作らないようにするため。
        if ($questions->isNotEmpty() && $answered >= $questions->count()) {
            return $this->finish($request, $questions);
        }

        return redirect()->route('quiz.index');
    }

    public function restart(Request $request): RedirectResponse
    {
        $request->session()->forget([self::ANSWERS, self::REVEALED]);

        return redirect()->route('quiz.index');
    }

    public function result(QuizAttempt $attempt): View
    {
        abort_unless($attempt->user_id === auth()->id(), 403);

        return view('quiz.result', [
            'attempt' => $attempt,
            'questions' => QuizQuestion::orderBy('id')->get(),
        ]);
    }

    /** @return Collection<int, QuizQuestion> */
    private function questions(): Collection
    {
        return QuizQuestion::orderBy('id')->get();
    }

    /**
     * 全問終わったら記録を残して結果画面へ送る。
     *
     * @param  Collection<int, QuizQuestion>  $questions
     */
    private function finish(Request $request, Collection $questions): RedirectResponse
    {
        $answers = $request->session()->get(self::ANSWERS, []);

        $score = $questions
            ->filter(fn (QuizQuestion $q) => ($answers[$q->id] ?? null) === $q->correct_index)
            ->count();

        $attempt = QuizAttempt::create([
            'user_id' => $request->user()->id,
            'score' => $score,
            'total' => $questions->count(),
            'answers' => $answers,
        ]);

        $request->session()->forget([self::ANSWERS, self::REVEALED]);

        return redirect()->route('quiz.result', $attempt);
    }
}
