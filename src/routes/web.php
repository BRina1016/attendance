<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StampController;
use App\Http\Controllers\RestController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ListController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () { return view('index'); })->middleware('auth'); // ログイン必須
Route::get('/list', [ListController::class, 'userList'])->name('user.list')->middleware('auth');
Route::get('/list/{user}/{year?}/{month?}', [ListController::class, 'showUser'])->name('user.show')->middleware('auth');
Route::get('/attendance/{date?}', [AttendanceController::class, 'searchByDate'])->name('attendance.search')->middleware('auth');
Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index')->middleware('auth');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ログイン関連のルートはゲストのみアクセス可能
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// 認証後のルート
Route::middleware('auth')->group(function () {
    Route::post('/stamp/clock_in', [StampController::class, 'clock_in']);
    Route::post('/stamp/clock_out', [StampController::class, 'clock_out']);
    Route::post('/stamp/rest_start', [RestController::class, 'rest_start']);
    Route::post('/stamp/rest_end', [RestController::class, 'rest_end']);
    Route::get('/stamp/status', [StampController::class, 'status']);
});

// 追加のミドルウェア
Route::middleware(['cors'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    });

    Route::get('/profile', function () {
        return view('profile');
    });
});
