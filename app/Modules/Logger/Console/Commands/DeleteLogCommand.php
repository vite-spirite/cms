<?php

namespace App\Modules\Logger\Console\Commands;

use App\Modules\Logger\Models\Logger;

class DeleteLogCommand extends \Illuminate\Console\Command
{
    protected $signature = 'logger:prune {--days= : Number of days start deleting}';
    protected $description = 'Delete log entries';

    public function handle(): int
    {
        $days = $this->option('days') ?? 30;

        $date = now()->subDays($days)->endOfDay();
        $this->info("Deletion of logs before {$date->toDateTimeString()}");

        Logger::where('created_at', '<', $date->toDateTimeString())->delete();
        return 1;
    }
}
