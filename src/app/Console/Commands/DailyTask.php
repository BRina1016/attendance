<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class DailyTask extends Command
{
    protected $signature = 'daily:task';
    protected $description = '日付が変わった際に毎日実行するタスク';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $now = Carbon::now();
        Log::info('DailyTask started at ' . $now);

        // 勤務終了を押していない場合、終了時刻を保存
        $updatedStamps = DB::table('stamps')
            ->whereNull('clock_out')
            ->update([
                'clock_out' => $now, 
                'work_time' => DB::raw('TIMESTAMPDIFF(SECOND, clock_in, clock_out)')
            ]);
        Log::info($updatedStamps . ' stamps updated with clock_out.');

        // 休憩開始を押していて休憩終了・勤務終了を押していない場合、休憩と勤務を終了
        $stamps = DB::table('stamps')
            ->whereNull('clock_out')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('rests')
                    ->whereColumn('stamps.stamp_id', 'rests.stamp_id')
                    ->whereNull('rests.rest_end');
            })
            ->get();

        foreach ($stamps as $stamp) {
            $updatedRests = DB::table('rests')
                ->where('stamp_id', $stamp->stamp_id)
                ->whereNull('rest_end')
                ->update([
                    'rest_end' => $now, 
                    'rest_time' => DB::raw('TIMESTAMPDIFF(SECOND, rest_start, rest_end)')
                ]);
            Log::info($updatedRests . ' rests updated with rest_end for stamp_id ' . $stamp->stamp_id);

            $updatedStamps = DB::table('stamps')
                ->where('stamp_id', $stamp->stamp_id)
                ->update([
                    'clock_out' => $now, 
                    'work_time' => DB::raw('TIMESTAMPDIFF(SECOND, clock_in, clock_out)')
                ]);
            Log::info($updatedStamps . ' stamps updated with clock_out for stamp_id ' . $stamp->stamp_id);
        }

        // 勤務開始ボタンを再び押せるように `button_states` テーブルをリセット
        DB::table('button_states')->truncate();
        Log::info('button_states table truncated.');

        $this->info('日次タスクが正常に完了しました。');
        Log::info('DailyTask completed at ' . Carbon::now()->toDateTimeString());
    }
}
