<?php

use App\Models\DrillResponse;
use App\Models\Persona;
use App\Models\Scenario;
use App\Models\User;
use Database\Seeders\PilotResponsesSeeder;

it('パイロット用の比較回答を重複なく準備する', function () {
    $persona = Persona::create([
        'name' => '相談者', 'role' => '若手社員', 'background' => '背景',
        'challenge' => '課題', 'tone' => '慎重',
    ]);
    Scenario::create([
        'persona_id' => $persona->id, 'title' => 'パイロットケース',
        'situation' => '状況', 'goal' => '目標', 'difficulty' => '初級',
    ]);

    $this->seed(PilotResponsesSeeder::class);
    $this->seed(PilotResponsesSeeder::class);

    expect(User::whereIn('email', PilotResponsesSeeder::EMAILS)->count())->toBe(3)
        ->and(DrillResponse::count())->toBe(3);
});
