<?php

use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\User;

/**
 * 出題順が固定されるよう id 順に作る。正解はすべて2番目（index 1）。
 */
function makeQuestions(int $count = 3): void
{
    foreach (range(1, $count) as $i) {
        QuizQuestion::create([
            'category' => 'テスト',
            'question' => "設問{$i}",
            'choices' => ['えらぶア', 'えらぶイ', 'えらぶウ', 'えらぶエ'],
            'correct_index' => 1,
            'explanation' => "かいせつ{$i}",
        ]);
    }
}

/**
 * 回答を送る。値はすべて文字列にする。
 * ブラウザのフォームから届くのは文字列であり、ここを整数で渡すと
 * 型の食い違いによる不具合をテストが見逃す（実際に見逃した）。
 */
function answer(QuizQuestion $question, int $choice)
{
    return test()->post(route('quiz.answer'), [
        'question_id' => (string) $question->id,
        'choice' => (string) $choice,
    ]);
}

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('1問目だけを出し、回答するまで解説を見せない', function () {
    makeQuestions();

    $this->get(route('quiz.index'))
        ->assertOk()
        ->assertSee('設問1')
        ->assertDontSee('設問2')
        ->assertDontSee('かいせつ1');
});

it('正解を選ぶと正解と表示され、解説が出る', function () {
    makeQuestions();

    answer(QuizQuestion::first(), 1)->assertRedirect(route('quiz.index'));

    $this->get(route('quiz.index'))
        ->assertSee('正解です')
        ->assertDontSee('おしいです')
        ->assertSee('かいせつ1');
});

it('間違えたときもその場で解説が出る', function () {
    makeQuestions();

    answer(QuizQuestion::first(), 3);

    $this->get(route('quiz.index'))
        ->assertSee('おしいです')
        ->assertDontSee('正解です')
        ->assertSee('かいせつ1');
});

it('次へで2問目に進む', function () {
    makeQuestions();

    answer(QuizQuestion::first(), 1);
    $this->post(route('quiz.next'))->assertRedirect(route('quiz.index'));

    $this->get(route('quiz.index'))
        ->assertSee('設問2')
        ->assertDontSee('かいせつ2');
});

it('同じ設問に二度答えても最初の回答が残る', function () {
    makeQuestions();
    $question = QuizQuestion::first();

    answer($question, 3);
    answer($question, 1);

    // 取り直しが効いていれば「正解です」に変わってしまう
    $this->get(route('quiz.index'))->assertSee('おしいです');
});

it('全問正解すると満点が記録され、結果画面へ進む', function () {
    makeQuestions();

    foreach (QuizQuestion::orderBy('id')->get() as $question) {
        answer($question, 1);
        $response = $this->post(route('quiz.next'));
    }

    $attempt = QuizAttempt::where('user_id', $this->user->id)->firstOrFail();

    expect($attempt->score)->toBe(3)
        ->and($attempt->total)->toBe(3);

    $response->assertRedirect(route('quiz.result', $attempt));

    // 得点はタグをまたいで出力されるので、タグを除いた本文で確認する
    $this->get(route('quiz.result', $attempt))
        ->assertOk()
        ->assertSeeText('3 / 3 問正解');
});

it('間違えた分は得点に入らない', function () {
    makeQuestions();

    foreach (QuizQuestion::orderBy('id')->get() as $index => $question) {
        answer($question, $index === 0 ? 1 : 0);
        $this->post(route('quiz.next'));
    }

    expect(QuizAttempt::where('user_id', $this->user->id)->firstOrFail()->score)->toBe(1);
});

it('やり直すと1問目に戻る', function () {
    makeQuestions();

    answer(QuizQuestion::first(), 1);
    $this->post(route('quiz.next'));
    $this->post(route('quiz.restart'))->assertRedirect(route('quiz.index'));

    $this->get(route('quiz.index'))
        ->assertSee('設問1')
        ->assertDontSee('かいせつ1');
});

it('他人の結果は見られない', function () {
    makeQuestions();
    $attempt = QuizAttempt::create([
        'user_id' => User::factory()->create()->id,
        'score' => 1, 'total' => 3, 'answers' => [],
    ]);

    $this->get(route('quiz.result', $attempt))->assertForbidden();
});

it('設問が無いときは準備中と伝える', function () {
    $this->get(route('quiz.index'))
        ->assertOk()
        ->assertSee('クイズは準備中です');
});
