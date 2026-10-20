<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EditorController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ManuscriptController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewerController;
use App\Http\Controllers\ReviewInvitationController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route(auth()->check() ? 'welcome' : 'login'));

Route::middleware('guest')->group(function () {
    Route::get('/users/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/users/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/users/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/users/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::get('/profile/forgot-password', [AuthController::class, 'showForgot'])->name('password.request');
    Route::post('/profile/forgot-password', [AuthController::class, 'sendReset'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/profile/reset-password/{token}', [AuthController::class, 'showReset'])->name('password.reset');
    Route::post('/profile/reset-password', [AuthController::class, 'reset'])->name('password.update');
});

Route::get('/users/confirm', [AuthController::class, 'confirm'])->name('confirm');
Route::get('/users/thank-you', fn () => view('auth.thank-you'))->name('thank-you');

// Agree / decline link from the invitation e-mail: no login, one-time token (L-link decision 2026-10-20)
Route::get('/review/{token}', [ReviewInvitationController::class, 'show'])->name('review.respond');
Route::post('/review/{token}', [ReviewInvitationController::class, 'respond'])->middleware('throttle:30,1');

Route::middleware('auth')->group(function () {
    Route::post('/users/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/users/welcome', fn () => view('auth.welcome'))->name('welcome');
    Route::get('/manuscripts', [ManuscriptController::class, 'index'])->name('manuscripts.index');
    Route::get('/manuscripts/submit', [ManuscriptController::class, 'create'])->name('manuscripts.create');
    Route::post('/manuscripts/submit', [ManuscriptController::class, 'store'])->name('manuscripts.store');
    Route::get('/manuscripts/{manuscript}/corrected', [ManuscriptController::class, 'showCorrected'])->whereNumber('manuscript')->name('manuscripts.corrected');
    Route::post('/manuscripts/{manuscript}/corrected', [ManuscriptController::class, 'storeCorrected'])->whereNumber('manuscript');
    Route::get('/manuscripts/{manuscript}/figures', [ManuscriptController::class, 'showFigures'])->whereNumber('manuscript')->name('manuscripts.figures');
    Route::post('/manuscripts/{manuscript}/figures', [ManuscriptController::class, 'storeCorrectedFigures'])->whereNumber('manuscript');
    Route::get('/files/{file}', [FileController::class, 'show'])->whereNumber('file')->name('files.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update']);
    Route::get('/profile/change-password', [ProfileController::class, 'showPassword'])->name('profile.password');
    Route::post('/profile/change-password', [ProfileController::class, 'updatePassword']);

    // Reviewer: own assignments and review upload (ManuscriptPolicy::uploadReview)
    Route::get('/reviews', [ReviewerController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/{review}/upload', [ReviewerController::class, 'create'])->whereNumber('review')->name('reviews.upload');
    Route::post('/reviews/{review}/upload', [ReviewerController::class, 'store'])->whereNumber('review');
    Route::get('/help/{name}', [ReviewerController::class, 'help'])->where('name', 'checklist|instructions')->name('reviews.help');

    // Editorial desk – editors only (ManuscriptPolicy::editorial)
    Route::middleware('can:editorial,App\Models\Manuscript')->prefix('editor')->group(function () {
        Route::get('/lists/{list}', [EditorController::class, 'index'])->where('list', 'inprogress|rejected|published')->name('editor.list');
        Route::prefix('manuscripts/{manuscript}')->whereNumber('manuscript')->group(function () {
            Route::get('/reviewers/{slot}', [EditorController::class, 'assignForm'])->whereIn('slot', [1, 2, 3])->name('editor.assign');
            Route::post('/reviewers/{slot}', [EditorController::class, 'assign'])->whereIn('slot', [1, 2, 3]);
            Route::post('/reviewers/{slot}/resend', [EditorController::class, 'resend'])->whereIn('slot', [1, 2, 3])->name('editor.resend');
            Route::get('/reminder/{slot}', [EditorController::class, 'reminderForm'])->whereIn('slot', [1, 2, 3])->name('editor.reminder');
            Route::post('/reminder/{slot}', [EditorController::class, 'reminder'])->whereIn('slot', [1, 2, 3]);
            Route::get('/decision', [EditorController::class, 'decisionForm'])->name('editor.decision');
            Route::post('/decision', [EditorController::class, 'decision']);
            Route::get('/reject', [EditorController::class, 'rejectForm'])->name('editor.reject');
            Route::post('/reject', [EditorController::class, 'reject']);
            Route::get('/status', [EditorController::class, 'statusForm'])->name('editor.status');
            Route::post('/status', [EditorController::class, 'status']);
            Route::post('/status/letters', [EditorController::class, 'sendLetters'])->name('editor.letters');
            Route::get('/proof', [EditorController::class, 'proofForm'])->name('editor.proof');
            Route::post('/proof', [EditorController::class, 'proof']);
        });
    });
});
