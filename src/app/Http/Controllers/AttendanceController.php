<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stamp;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function searchByDate(Request $request, $date = null)
    {
        $date = $date ?: Carbon::today()->toDateString();
        $currentDate = Carbon::parse($date);
        $prevDate = $currentDate->copy()->subDay()->toDateString();
        $nextDate = $currentDate->copy()->addDay()->toDateString();

        $stamps = Stamp::whereDate('clock_in', $date)->with('user', 'rests')->paginate(5);

        return view('attendance', compact('currentDate', 'prevDate', 'nextDate', 'stamps'));
    }
}
