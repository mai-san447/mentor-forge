<?php

namespace Database\Seeders;

use App\Models\DrillResponse;
use App\Models\Scenario;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PilotResponsesSeeder extends Seeder
{
    public const EMAILS = [
        'pilot-peer-a@example.invalid',
        'pilot-peer-b@example.invalid',
        'pilot-peer-c@example.invalid',
    ];

    public function run(): void
    {
        $responses = [
            '最近ミスが続いていることを、今どんなふうに感じていますか？',
            '話せる範囲で大丈夫です。周りに相談しにくいと感じるのは、どんなときでしょうか。',
            '今すぐ答えを決めなくても大丈夫です。まず、いちばん気になっていることから一緒に整理しませんか。',
        ];

        foreach (self::EMAILS as $index => $email) {
            $user = User::firstOrCreate(['email' => $email], [
                'name' => 'パイロット比較回答'.chr(65 + $index),
                'password' => Hash::make(Str::random(40)),
                'email_verified_at' => now(),
            ]);

            foreach (Scenario::all() as $scenario) {
                DrillResponse::firstOrCreate(
                    ['user_id' => $user->id, 'scenario_id' => $scenario->id],
                    ['content' => $responses[$index]],
                );
            }
        }
    }
}
