<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function searchByDate(Request $request, $date = null)
{
    $date = $date ?: Carbon::today()->toDateString();
    $currentDate = Carbon::parse($date)->toDateString();
    $prevDate = Carbon::parse($date)->subDay()->toDateString();
    $nextDate = Carbon::parse($date)->addDay()->toDateString();

    $stamps = [
        (object)[
            'user' => (object)['name' => '山田太郎'],
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'rests' => [(object)['rest_time' => '12:00']],
            'work_time' => '08:00'
        ],
    ];

    return view('attendance', compact('currentDate', 'prevDate', 'nextDate', 'stamps'));
}
}
