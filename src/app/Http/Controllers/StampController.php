<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Stamp;
use App\Models\Rest;
use App\Models\User;

class StampController extends Controller
{
    public function clockIn(Request $request)
    {
        $userId = Auth::id();
        $today = Carbon::today();

        // すでに今日の勤務開始が記録されているかチェック
        $alreadyClockedIn = Stamp::where('user_id', $userId)
                                    ->whereDate('clock_in', $today)
                                    ->exists();

        if ($alreadyClockedIn) {
            return response()->json(['status' => 'already_clocked_in'], 400);
        }

        // 勤務開始を記録
        $stamp = new Stamp();
        $stamp->user_id = $userId;
        $stamp->clock_in = now();
        $stamp->save();

        return response()->json(['status' => 'clock_in_success']);
    }

    public function checkStatus(Request $request)
    {
        $userId = Auth::id();
        $today = Carbon::today();

        $alreadyClockedIn = Stamp::where('user_id', $userId)
                                    ->whereDate('clock_in', $today)
                                    ->exists();

        return response()->json(['status' => $alreadyClockedIn ? 'already_clocked_in' : 'not_clocked_in']);
    }

    public function clock_in(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            try {
                $existingStamp = Stamp::where('user_id', $user->id)
                                        ->whereDate('clock_in', now()->toDateString())
                                        ->first();

                if ($existingStamp) {
                    return response()->json(['status' => 'error', 'message' => 'You have already clocked in today']);
                }

                $stamp = Stamp::create([
                    'user_id' => $user->id,
                    'clock_in' => now(),
                    'date' => now()->toDateString(),
                ]);

                return response()->json(['status' => 'success', 'message' => 'Clock In successful']);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => 'Failed to save clock in: ' . $e->getMessage()]);
            }
        }

        return response()->json(['status' => 'error', 'message' => 'User not authenticated']);
    }

    public function clock_out(Request $request)
    {
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

                // 休憩中なら休憩終了時間を勤務終了時間と同じにする
                $rest = Rest::where('stamp_id', $stamp->id)->whereNull('rest_end')->first();
                if ($rest) {
                    $rest->rest_end = $stamp->clock_out;
                    $rest->save();
                }

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

    public function status(Request $request)
    {
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

    public function clockOut(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not authenticated']);
        }

        $stamp = Stamp::where('user_id', $user->id)->whereNull('clock_out')->first();
        if ($stamp) {
            $now = Carbon::now();

            // 休憩中であれば、休憩終了時間を勤務終了時間と同じにする
            $rest = Rest::where('stamp_id', $stamp->id)->whereNull('rest_end')->first();
            if ($rest) {
                $rest->rest_end = $now;
                $rest->rest_time = $rest->rest_start->diffInSeconds($rest->rest_end);
                $rest->save();
            }

            // 勤務終了時間を記録
            $stamp->clock_out = $now;
            $stamp->work_time = $stamp->clock_in->diffInSeconds($stamp->clock_out);
            $stamp->save();

            return response()->json(['status' => 'success', 'message' => 'Clock Out successful']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'No active clock in found']);
        }
    }

    public function handleEndOfDay()
    {
        $users = User::all();
        $yesterday = Carbon::yesterday()->format('Y-m-d');

        foreach ($users as $user) {
            $stamp = Stamp::where('user_id', $user->id)
                            ->whereNull('clock_out')
                            ->whereDate('clock_in', $yesterday)
                            ->first();

            if ($stamp) {
                $stamp->clock_out = Carbon::createFromFormat('Y-m-d H:i:s', "$yesterday 23:59:59");
                $stamp->work_time = $stamp->clock_in->diffInSeconds($stamp->clock_out);
                $stamp->save();

                $rest = Rest::where('stamp_id', $stamp->id)
                            ->whereNull('rest_end')
                            ->first();

                if ($rest) {
                    $rest->rest_end = Carbon::createFromFormat('Y-m-d H:i:s', "$yesterday 23:59:59");
                    $rest->rest_time = $rest->rest_start->diffInSeconds($rest->rest_end);
                    $rest->save();
                }
            }
        }
    }
}
