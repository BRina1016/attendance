<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StampController;
use App\Http\Controllers\RestController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LoginController;


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

Route::get('/', function () { return view('index'); });
Route::get('/attendance/{date?}', [AttendanceController::class, 'searchByDate'])->name('attendance.search');
Route::get('/login', [StampController::class, 'login']);
Route::get('/register', [RegisterController::class, 'register']);

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::post('/stamp/clock_in', [StampController::class, 'clock_in']);
    Route::post('/stamp/clock_out', [StampController::class, 'clock_out']);
    Route::post('/stamp/rest_start', [RestController::class, 'rest_start']);
    Route::post('/stamp/rest_end', [RestController::class, 'rest_end']);
    Route::get('/stamp/status', [StampController::class, 'status']);
});
