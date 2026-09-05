<?php

use App\Models\CaseReflection;
use App\Models\DrillResponse;
use App\Models\Persona;
use App\Models\Scenario;
use App\Models\User;

function makeReflectionScenario(string $title = '振り返りケース'): Scenario
{
    $persona = Persona::create([
        'name' => '相談者',
        'role' => '若手社員',
        'background' => '今後のキャリアに迷っている。',
        'challenge' => '自分の希望を言葉にできない',
        'tone' => '慎重',
    ]);

    return Scenario::create([
        'persona_id' => $persona->id,
        'title' => $title,
        'situation' => '面談で今後の希望を聞かれた。',
        'goal' => '本人の考えを引き出す',
        'difficulty' => '初級',
    ]);
}

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->other = User::factory()->create(['name' => '匿名にする名前']);
    $this->scenario = makeReflectionScenario();
    $this->ownResponse = DrillResponse::create([
        'user_id' => $this->user->id,
        'scenario_id' => $this->scenario->id,
        'content' => 'まず希望を聞きます。',
    ]);
    $this->otherResponse = DrillResponse::create([
        'user_id' => $this->other->id,
        'scenario_id' => $this->scenario->id,
        'content' => '迷いが生まれた背景を一緒に整理します。',
    ]);
    $this->actingAs($this->user);
});

function reflectionPayload(DrillResponse $selected): array
{
    return [
        'selected_response_id' => (string) $selected->id,
        'selection_reason' => '背景から聴く視点が自分になかったから。',
        'difference' => '自分は希望を直接聞き、相手は迷いの背景から聞いている。',
        'next_action' => '次の面談では、迷いが生まれたきっかけを先に尋ねる。',
    ];
}

it('匿名回答を選び理由と違いと次の行動を保存する', function () {
    $this->post(route('cases.reflection.store', $this->scenario), reflectionPayload($this->otherResponse))
        ->assertRedirect(route('cases.show', $this->scenario));

    $this->assertDatabaseHas('case_reflections', [
        'user_id' => $this->user->id,
        'scenario_id' => $this->scenario->id,
        'selected_response_id' => $this->otherResponse->id,
        'selected_response_content' => '迷いが生まれた背景を一緒に整理します。',
        'next_action' => '次の面談では、迷いが生まれたきっかけを先に尋ねる。',
    ]);
});

it('保存した振り返りを後から確認でき個人情報を表示しない', function () {
    CaseReflection::create([
        'user_id' => $this->user->id,
        'scenario_id' => $this->scenario->id,
        'selected_response_id' => $this->otherResponse->id,
        'selected_response_content' => $this->otherResponse->content,
        'selection_reason' => '背景を見る視点',
        'difference' => '質問の順番',
        'next_action' => '背景から尋ねる',
    ]);

    $this->get(route('cases.show', $this->scenario))
        ->assertOk()
        ->assertSee('保存済みの振り返り')
        ->assertSee('背景を見る視点')
        ->assertSee('質問の順番')
        ->assertSee('背景から尋ねる')
        ->assertDontSee('匿名にする名前')
        ->assertDontSee($this->other->email);
});

it('自分が回答する前は振り返りを保存できない', function () {
    DrillResponse::whereKey($this->ownResponse->id)->delete();

    $this->post(route('cases.reflection.store', $this->scenario), reflectionPayload($this->otherResponse))
        ->assertForbidden();

    expect(CaseReflection::count())->toBe(0);
});

it('自分の回答は選択できない', function () {
    $this->post(route('cases.reflection.store', $this->scenario), reflectionPayload($this->ownResponse))
        ->assertNotFound();

    expect(CaseReflection::count())->toBe(0);
});

it('別のケースの回答は選択できない', function () {
    $anotherScenario = makeReflectionScenario('別ケース');
    $anotherResponse = DrillResponse::create([
        'user_id' => $this->other->id,
        'scenario_id' => $anotherScenario->id,
        'content' => '別ケースの回答',
    ]);

    $this->post(route('cases.reflection.store', $this->scenario), reflectionPayload($anotherResponse))
        ->assertNotFound();
});

it('振り返りの必須項目と文字数を検証する', function () {
    $this->post(route('cases.reflection.store', $this->scenario), [
        'selected_response_id' => $this->otherResponse->id,
        'selection_reason' => '',
        'difference' => str_repeat('あ', 2001),
        'next_action' => str_repeat('い', 1001),
    ])->assertSessionHasErrors(['selection_reason', 'difference', 'next_action']);
});

it('同じケースの振り返りを二重保存できない', function () {
    $this->post(route('cases.reflection.store', $this->scenario), reflectionPayload($this->otherResponse));

    $this->post(route('cases.reflection.store', $this->scenario), reflectionPayload($this->otherResponse))
        ->assertRedirect(route('cases.show', $this->scenario))
        ->assertSessionHasErrors('reflection');

    expect(CaseReflection::where('user_id', $this->user->id)->count())->toBe(1);
});

it('マイページに振り返り履歴と次の行動を表示する', function () {
    CaseReflection::create([
        'user_id' => $this->user->id,
        'scenario_id' => $this->scenario->id,
        'selected_response_id' => $this->otherResponse->id,
        'selected_response_content' => $this->otherResponse->content,
        'selection_reason' => '理由',
        'difference' => '違い',
        'next_action' => '背景から尋ねる',
    ]);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('ケースドリルの学習履歴')
        ->assertSee('振り返りケース')
        ->assertSee('背景から尋ねる');
});
