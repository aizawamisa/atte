<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// ログインホーム
Route::get('/', [AttendanceController::class, 'punch'])->middleware('auth', 'verified')->name('index');

// 打刻機能
Route::post('/start-work', [AttendanceController::class, 'startWork'])->name('startWork');
Route::post('/end-work', [AttendanceController::class, 'endWork'])->name('endWork');
Route::post('/start-rest', [AttendanceController::class, 'startRest'])->name('startRest');
Route::post('/end-rest', [AttendanceController::class, 'endRest'])->name('endRest');

// ログアウト
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/logout', [AuthController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// 日付一覧
Route::get('/attendance/date', [AttendanceController::class, 'indexDate'])->name('attendance/date');
Route::post('/attendance/date', [AttendanceController::class, 'perDate'])->name('per/date');

