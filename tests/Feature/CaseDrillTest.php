<?php

use App\Models\DrillResponse;
use App\Models\Persona;
use App\Models\Scenario;
use App\Models\User;

function makeScenario(): Scenario
{
    $persona = Persona::create([
        'name' => '相談者A',
        'role' => '入社2年目',
        'background' => '新しい仕事に自信をなくしている。',
        'challenge' => '周囲に相談できない',
        'tone' => '慎重',
    ]);

    return Scenario::create([
        'persona_id' => $persona->id,
        'title' => 'テストケース',
        'situation' => '面談で相手が黙り込んだ。',
        'goal' => '安心して話せる状態をつくる',
        'difficulty' => '初級',
    ]);
}

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->scenario = makeScenario();
});

it('ケース一覧と詳細を表示する', function () {
    $this->get(route('cases.index'))
        ->assertOk()
        ->assertSee('テストケース')
        ->assertSee('未回答');

    $this->get(route('cases.show', $this->scenario))
        ->assertOk()
        ->assertSee('面談で相手が黙り込んだ。')
        ->assertSee('あなたなら、最初にどう返しますか？');
});

it('自由回答を投稿できる', function () {
    $this->post(route('cases.responses.store', $this->scenario), [
        'content' => '今、どんなことを考えていますか？',
    ])->assertRedirect(route('cases.show', $this->scenario));

    $this->assertDatabaseHas('drill_responses', [
        'user_id' => $this->user->id,
        'scenario_id' => $this->scenario->id,
        'content' => '今、どんなことを考えていますか？',
    ]);
});

it('未回答者には他者回答の本文も個人情報も渡さない', function () {
    $other = User::factory()->create(['name' => '表示してはいけない名前']);
    DrillResponse::create([
        'user_id' => $other->id,
        'scenario_id' => $this->scenario->id,
        'content' => '表示してはいけない回答',
    ]);

    $this->get(route('cases.show', $this->scenario))
        ->assertOk()
        ->assertDontSee('表示してはいけない回答')
        ->assertDontSee('表示してはいけない名前')
        ->assertSee('ほかの人の回答はまだ見られません');
});

it('回答後は他者回答を匿名表示し自分の回答を識別する', function () {
    $other = User::factory()->create(['name' => '表示してはいけない名前']);
    DrillResponse::create([
        'user_id' => $other->id,
        'scenario_id' => $this->scenario->id,
        'content' => '相手の気持ちをまず確認します。',
    ]);
    DrillResponse::create([
        'user_id' => $this->user->id,
        'scenario_id' => $this->scenario->id,
        'content' => '沈黙を待ちます。',
    ]);

    $this->get(route('cases.show', $this->scenario))
        ->assertOk()
        ->assertSee('相手の気持ちをまず確認します。')
        ->assertSee('匿名の回答 A')
        ->assertSee('あなたの回答')
        ->assertSee('沈黙を待ちます。')
        ->assertDontSee('表示してはいけない名前');
});

it('同じ利用者の二重投稿を防ぐ', function () {
    DrillResponse::create([
        'user_id' => $this->user->id,
        'scenario_id' => $this->scenario->id,
        'content' => '最初の回答',
    ]);

    $this->post(route('cases.responses.store', $this->scenario), ['content' => '上書き回答'])
        ->assertRedirect(route('cases.show', $this->scenario))
        ->assertSessionHasErrors('content');

    expect(DrillResponse::where('user_id', $this->user->id)->count())->toBe(1)
        ->and(DrillResponse::where('user_id', $this->user->id)->first()->content)->toBe('最初の回答');
});

it('他人の回答を編集する経路を持たない', function () {
    $otherResponse = DrillResponse::create([
        'user_id' => User::factory()->create()->id,
        'scenario_id' => $this->scenario->id,
        'content' => '他人の回答',
    ]);

    $this->patch("/cases/{$this->scenario->id}/responses/{$otherResponse->id}", [
        'content' => '書き換え',
    ])->assertNotFound();

    expect($otherResponse->fresh()->content)->toBe('他人の回答');
});

it('回答本文を必須かつ2000文字以内にする', function () {
    $this->post(route('cases.responses.store', $this->scenario), ['content' => ''])
        ->assertSessionHasErrors('content');

    $this->post(route('cases.responses.store', $this->scenario), ['content' => str_repeat('あ', 2001)])
        ->assertSessionHasErrors('content');
});

it('未ログインではケースを閲覧も投稿もできない', function () {
    auth()->logout();

    $this->get(route('cases.index'))->assertRedirect(route('login'));
    $this->get(route('cases.show', $this->scenario))->assertRedirect(route('login'));
    $this->post(route('cases.responses.store', $this->scenario), ['content' => '回答'])
        ->assertRedirect(route('login'));
});
