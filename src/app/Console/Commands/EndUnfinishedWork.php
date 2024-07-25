<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Stamp;
use Carbon\Carbon;

class EndUnfinishedWork extends Command
{
    protected $signature = 'work:end_unfinished';
    protected $description = 'End unfinished work at the end of the day';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $unfinishedStamps = Stamp::whereNull('clock_out')->get();

        foreach ($unfinishedStamps as $stamp) {
            $stamp->clock_out = Carbon::now()->startOfDay(); // 日付が変わった時点の0時を設定
            $seconds = $stamp->clock_in->diffInSeconds($stamp->clock_out);
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            $formattedTime = sprintf('%02d:%02d', $hours, $minutes);
            $stamp->work_time = $formattedTime;
            $stamp->save();
        }

        $this->info('Unfinished work ended successfully');
    }
}