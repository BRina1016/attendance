<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Stamp;
use App\Models\Rest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ListController extends Controller
{
    public function userList()
    {
        $users = User::paginate(50);
        return view('list', compact('users'));
    }

    public function showUser(User $user, $year = null, $month = null)
{
    $year = $year ?? Carbon::now()->year;
    $month = $month ?? Carbon::now()->month;

    $stamps = Stamp::where('user_id', $user->id)
        ->whereYear('clock_in', $year)
        ->whereMonth('clock_in', $month)
        ->with('rests')
        ->paginate(5);

    $previousMonth = Carbon::create($year, $month, 1)->subMonth();
    $nextMonth = Carbon::create($year, $month, 1)->addMonth();

    return view('show', compact('user', 'stamps', 'previousMonth', 'nextMonth', 'year', 'month'));
}


}