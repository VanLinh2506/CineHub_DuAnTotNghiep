<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupOldLogs extends Command
{
    protected $signature = 'logs:cleanup {--days=30 : Number of days to keep}';
    protected $description = 'Clean up old admin logs';

    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);
        
        try {
            $deleted = DB::table('admin_logs')
                ->where('created_at', '<', $cutoffDate)
                ->delete();
                
            $this->info("Cleaned up {$deleted} old log(s) (older than {$days} days).");
        } catch (\Exception $e) {
            $this->error("Table admin_logs may not exist yet.");
        }
        
        return 0;
    }
}
