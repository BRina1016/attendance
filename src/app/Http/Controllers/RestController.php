<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Rest;
use App\Models\Stamp;
use Carbon\Carbon;

class RestController extends Controller
{
    public function rest_start(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $stamp = Stamp::where('user_id', $user->id)->whereNull('clock_out')->first();
            if ($stamp) {
                $rest = Rest::create([
                    'user_id' => $user->id,
                    'stamp_id' => $stamp->stamp_id,
                    'rest_start' => now(),
                ]);

                if ($rest) {
                    return response()->json(['status' => 'success', 'message' => 'Rest Start successful']);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Failed to save rest start']);
                }
            } else {
                return response()->json(['status' => 'error', 'message' => 'No active clock in found']);
            }
        }

        return response()->json(['status' => 'error', 'message' => 'User not authenticated']);
    }

    public function rest_end(Request $request)
{
    $user = Auth::user();
    if ($user) {
        $stamp = Stamp::where('user_id', $user->id)->whereNull('clock_out')->first();
        if ($stamp) {
            $rest = Rest::where('user_id', $user->id)->where('stamp_id', $stamp->stamp_id)->whereNull('rest_end')->first();
            if ($rest) {
                $rest->rest_end = now();
                $seconds = $rest->rest_start->diffInSeconds($rest->rest_end);  // ここで diffInSeconds() を使用
                $hours = floor($seconds / 3600);
                $minutes = floor(($seconds % 3600) / 60);

                $formattedTime = sprintf('%02d:%02d', $hours, $minutes);
                $rest->rest_time = $formattedTime;

                if ($rest->save()) {
                    return response()->json(['status' => 'success', 'message' => 'Rest End successful']);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'Failed to save rest end']);
                }
            } else {
                return response()->json(['status' => 'error', 'message' => 'No active rest found']);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'No active clock in found']);
        }
    }
    return response()->json(['status' => 'error', 'message' => 'User not authenticated']);
}

}
