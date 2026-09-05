<?php

return [
    // 自分で本番動線を確認する間だけ有効にする。外部テスト前に必ず false へ戻す。
    'enabled' => (bool) env('PILOT_TEST_MODE', false),
    'tester_email' => env('PILOT_TESTER_EMAIL'),
];
