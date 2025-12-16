<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\XPTransaction;

class ResetDailyBonus extends Command
{
    protected $signature = 'bonus:reset-daily {user_id=1}';
    protected $description = 'Reset daily completion bonus for testing';

    public function handle()
    {
        $userId = $this->argument('user_id');

        $deleted = XPTransaction::where('user_id', $userId)
            ->where('source_type', XPTransaction::SOURCE_DAILY_COMPLETION)
            ->whereDate('created_at', today())
            ->delete();

        $this->info("Deleted {$deleted} daily completion bonus(es) for user {$userId}");
        $this->warn("Note: This does NOT remove the XP already awarded. This is just for testing the bonus trigger.");

        return 0;
    }
}
