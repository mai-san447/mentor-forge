<?php

use App\Models\User;
use Database\Seeders\PilotResponsesSeeder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('pilot:prepare', function () {
    if (config('pilot.enabled') !== true || ! config('pilot.tester_email')) {
        $this->error('PILOT_TEST_MODE=true と PILOT_TESTER_EMAIL を設定して、config:cache をやり直してください。');

        return 1;
    }

    app(PilotResponsesSeeder::class)->run();
    $this->info('パイロット用の比較回答を準備しました。');

    return 0;
})->purpose('Prepare synthetic peer responses for the pilot tester');

Artisan::command('pilot:cleanup', function () {
    $deleted = User::whereIn('email', PilotResponsesSeeder::EMAILS)->delete();
    $this->info("パイロット用アカウントを {$deleted} 件削除しました。関連する比較回答も削除されます。");

    return 0;
})->purpose('Remove synthetic peer responses after pilot testing');
