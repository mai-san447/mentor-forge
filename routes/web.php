<?php

use App\Http\Controllers\CaseDrillController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\SoloPracticeController;
use App\Http\Controllers\TrioPracticeController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('about', 'about')->name('about');

// 未登録の人からも問い合わせを受けられるよう、認証を要求しない。
Route::get('contact', [ContactController::class, 'create'])->name('contact.create');
Route::post('contact', [ContactController::class, 'store'])
    ->middleware('throttle:3,10')
    ->name('contact.store');

// 受信箱は管理用。誰が開けるかは ContactController 側で判定する。
Route::get('contact/inbox', [ContactController::class, 'inbox'])
    ->middleware('auth')
    ->name('contact.inbox');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('cases', [CaseDrillController::class, 'index'])->name('cases.index');
    Route::get('cases/{scenario}', [CaseDrillController::class, 'show'])->name('cases.show');
    Route::post('cases/{scenario}/responses', [CaseDrillController::class, 'store'])->name('cases.responses.store');

    Route::get('solo', [SoloPracticeController::class, 'index'])->name('solo.index');
    Route::post('solo/start/{scenario}', [SoloPracticeController::class, 'start'])->name('solo.start');
    Route::get('solo/{session}', [SoloPracticeController::class, 'show'])->name('solo.show');
    Route::post('solo/{session}/message', [SoloPracticeController::class, 'message'])->name('solo.message');
    Route::post('solo/{session}/complete', [SoloPracticeController::class, 'complete'])->name('solo.complete');
    Route::get('solo/{session}/result', [SoloPracticeController::class, 'result'])->name('solo.result');

    Route::get('trio', [TrioPracticeController::class, 'index'])->name('trio.index');
    Route::post('trio', [TrioPracticeController::class, 'store'])->name('trio.store');
    Route::post('trio/join', [TrioPracticeController::class, 'join'])->name('trio.join');
    Route::get('trio/{session}', [TrioPracticeController::class, 'show'])->name('trio.show');
    Route::post('trio/{session}/feedback', [TrioPracticeController::class, 'feedback'])->name('trio.feedback');

    // 1問ずつ出して、答えた直後に解説を見せる。進行状況はセッションに持つ。
    Route::get('quiz', [QuizController::class, 'index'])->name('quiz.index');
    Route::post('quiz/answer', [QuizController::class, 'answer'])->name('quiz.answer');
    Route::post('quiz/next', [QuizController::class, 'next'])->name('quiz.next');
    Route::post('quiz/restart', [QuizController::class, 'restart'])->name('quiz.restart');
    Route::get('quiz/result/{attempt}', [QuizController::class, 'result'])->name('quiz.result');
});

require __DIR__.'/settings.php';
