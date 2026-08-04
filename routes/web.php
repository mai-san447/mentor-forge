<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\SoloPracticeController;
use App\Http\Controllers\TrioPracticeController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

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

    Route::get('quiz', [QuizController::class, 'index'])->name('quiz.index');
    Route::post('quiz', [QuizController::class, 'submit'])->name('quiz.submit');
    Route::get('quiz/result/{attempt}', [QuizController::class, 'result'])->name('quiz.result');
});

require __DIR__.'/settings.php';
