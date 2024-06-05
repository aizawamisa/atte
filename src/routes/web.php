<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);


// ログイン後ホーム（打刻ページ）
Route::get('/', [AttendanceController::class, 'punch'])->middleware('auth', 'verified')->name('index');

// 打刻機能
Route::post('/start-work', [AttendanceController::class, 'startWork'])->name('startWork');
Route::post('/end-work', [AttendanceController::class, 'endWork'])->name('endWork');
Route::post('/start-rest', [AttendanceController::class, 'startRest'])->name('startRest');
Route::post('/end-rest', [AttendanceController::class, 'endRest'])->name('endRest');

// ログアウト
Route::get('/logout', [AuthController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// 管理ページ / 日付別
Route::get('/attendance/date', [AttendanceController::class, 'indexDate'])->name('attendance/date');
Route::post('/attendance/date', [AttendanceController::class, 'perDate'])->name('per/date');


