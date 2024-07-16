<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stamp;
use Carbon\Carbon;
use Auth;

class AttendanceController extends Controller
{
    public function attendance(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $stamps = Stamp::with('rests')->where('user_id', $user->id)->get();
        $prevDate = now()->subDay()->toDateString();
        $currentDate = now()->toDateString();

        $stamps->transform(function ($stamp) {
            $stamp->created_at = Carbon::parse($stamp->created_at)->toDateString();
            return $stamp;
        });

        return view('attendance', compact('stamps', 'prevDate', 'currentDate'));
    }
}
