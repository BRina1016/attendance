<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Stamp;
use App\Models\Rest;
use Carbon\Carbon;

class StampController extends Controller
{

    public function clock_in(Request $request){
    $user = Auth::user();
    if ($user) {
        try {
            $stamp = Stamp::create([
                'user_id' => $user->id,
                'clock_in' => now(),
            ]);
            return response()->json(['status' => 'success', 'message' => 'Clock In successful']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to save clock in: ' . $e->getMessage()]);
        }
    }
    return response()->json(['status' => 'error', 'message' => 'User not authenticated']);
    }

    public function clock_out(Request $request){
    $user = Auth::user();
    if ($user) {
        $stamp = Stamp::where('user_id', $user->id)->whereNull('clock_out')->first();
        if ($stamp) {
            $stamp->clock_out = now();
            $seconds = $stamp->clock_in->diffInSeconds(now());
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);

            $formattedTime = sprintf('%02d:%02d', $hours, $minutes);

            $stamp->work_time = $formattedTime;
            if ($stamp->save()) {
                return response()->json(['status' => 'success', 'message' => 'Clock Out successful']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Failed to save clock out']);
            }
        } else {
            return response()->json(['status' => 'error', 'message' => 'No active clock in found']);
        }
    }
    return response()->json(['status' => 'error', 'message' => 'User not authenticated']);
    }

    public function status(Request $request){
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not authenticated']);
        }

        try {
            $stamp = Stamp::where('user_id', $user->id)->latest()->first();
            $status = [
                'clocked_in' => $stamp ? (bool) $stamp->clock_in : false,
                'clocked_out' => $stamp ? (bool) $stamp->clock_out : false,
                'resting' => false,
            ];

            if ($stamp) {
                $rest = Rest::where('user_id', $user->id)->where('stamp_id', $stamp->stamp_id)->latest()->first();
                if ($rest) {
                    $status['resting'] = !$rest->rest_end;
                }
            }

            return response()->json($status);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
