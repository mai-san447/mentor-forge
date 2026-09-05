<?php

use App\Models\CaseReflection;
use App\Models\DiagnosticAssessment;
use App\Models\Persona;
use App\Models\PracticeFollowUp;
use App\Models\Scenario;
use App\Models\User;
use Illuminate\Support\Carbon;

function issueFiveReflection(User $user): CaseReflection
{
    $persona = Persona::create([
        'name' => '相談者', 'role' => '若手社員', 'background' => '仕事に迷っている。',
        'challenge' => '考えを言葉にできない', 'tone' => '慎重',
    ]);
    $scenario = Scenario::create([
        'persona_id' => $persona->id, 'title' => '実践ケース', 'situation' => '面談中',
        'goal' => '考えを引き出す', 'difficulty' => '初級',
    ]);

    return CaseReflection::create([
        'user_id' => $user->id, 'scenario_id' => $scenario->id,
        'selected_response_content' => '背景から尋ねる', 'selection_reason' => '視点が違う',
        'difference' => '質問の順番', 'next_action' => '助言の前に気持ちを尋ねる',
    ]);
}

function diagnosisResponses(int $value = 3): array
{
    return array_fill_keys(array_keys(config('diagnosis.items')), $value);
}

it('利用前診断を保存するが合計点を表示しない', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->post(route('diagnosis.store'), [
        'phase' => 'pre', 'responses' => diagnosisResponses(),
    ])->assertRedirect(route('diagnosis.show'));

    $this->assertDatabaseHas('diagnostic_assessments', ['user_id' => $user->id, 'phase' => 'pre']);
    $this->actingAs($user)->get(route('diagnosis.show'))->assertOk()->assertSee('能力を採点するテストではありません')->assertDontSee('合計点');
});

it('ケース学習後に利用後診断を保存し項目別の変化を表示する', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    DiagnosticAssessment::create(['user_id' => $user->id, 'phase' => 'pre', 'responses' => diagnosisResponses(2)]);
    issueFiveReflection($user);

    $responses = diagnosisResponses(2);
    $responses['listen'] = 4;
    $this->post(route('diagnosis.store'), ['phase' => 'post', 'responses' => $responses])->assertRedirect(route('diagnosis.show'));
    $this->get(route('diagnosis.show'))->assertOk()->assertSee('あなたが感じた変化')->assertSee('以前よりそう感じる')->assertSee('変化なし');
});

it('ケース学習前には利用後診断を保存できない', function () {
    $user = User::factory()->create();
    DiagnosticAssessment::create(['user_id' => $user->id, 'phase' => 'pre', 'responses' => diagnosisResponses()]);

    $this->actingAs($user)->post(route('diagnosis.store'), ['phase' => 'post', 'responses' => diagnosisResponses()])->assertForbidden();
});

it('2週間後に実践の有無と反応と相談の深まりを保存する', function () {
    Carbon::setTestNow('2026-09-20 12:00:00');
    $user = User::factory()->create();
    $reflection = issueFiveReflection($user);
    $reflection->forceFill(['created_at' => now()->subWeeks(2)])->save();

    $this->actingAs($user)->post(route('follow-ups.store', [$reflection, 2]), [
        'practiced' => '1', 'counterpart_reaction' => '少し考えた後、本音を話してくれた。',
        'consultation_change' => 'deeper', 'note' => '待つ時間を増やす。',
    ])->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('practice_follow_ups', [
        'case_reflection_id' => $reflection->id, 'weeks_after' => 2, 'practiced' => true,
        'consultation_change' => 'deeper',
    ]);
});

it('期日前の記録と他人の振り返りへのアクセスを拒否する', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $reflection = issueFiveReflection($owner);

    $this->actingAs($owner)->post(route('follow-ups.store', [$reflection, 2]), ['practiced' => '0'])->assertForbidden();
    $this->actingAs($other)->get(route('follow-ups.show', [$reflection, 2]))->assertForbidden();
});

it('マイページに2週間後と4週間後の振り返り時期と完了を表示する', function () {
    $user = User::factory()->create();
    $reflection = issueFiveReflection($user);
    PracticeFollowUp::create([
        'user_id' => $user->id, 'case_reflection_id' => $reflection->id,
        'weeks_after' => 2, 'practiced' => false,
    ]);

    $this->actingAs($user)->get(route('dashboard'))->assertOk()
        ->assertSee('2週間後')->assertSee('4週間後')->assertSee('記録済み')->assertSee($reflection->created_at->copy()->addWeeks(4)->format('Y/m/d'));
});
