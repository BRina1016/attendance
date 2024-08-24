<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ButtonStateController extends Controller
{
    public function save(Request $request)
    {
        $userId = Auth::id();
        $buttonStates = $request->all();

        Log::info('Saving button states for user ' . $userId, $buttonStates);

        DB::table('button_states')->updateOrInsert(
            ['user_id' => $userId],
            ['states' => json_encode($buttonStates), 'updated_at' => now()]
        );

        return response()->json(['status' => 'success']);
    }

    public function get()
    {
        $userId = Auth::id();
        $buttonStates = DB::table('button_states')->where('user_id', $userId)->first();

        Log::info('Fetching button states for user ' . $userId);

        if ($buttonStates) {
            Log::info('Button states found', json_decode($buttonStates->states, true));
            return response()->json(json_decode($buttonStates->states, true));
        }

        Log::info('No button states found');
        return response()->json(null);
    }
}
