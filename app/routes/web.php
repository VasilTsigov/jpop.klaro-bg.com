<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProfileController;
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

Route::middleware('auth')->group(function () {
    Route::post('/users/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/users/welcome', fn () => view('auth.welcome'))->name('welcome');
    Route::get('/files/{file}', [FileController::class, 'show'])->whereNumber('file')->name('files.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update']);
    Route::get('/profile/change-password', [ProfileController::class, 'showPassword'])->name('profile.password');
    Route::post('/profile/change-password', [ProfileController::class, 'updatePassword']);
});
