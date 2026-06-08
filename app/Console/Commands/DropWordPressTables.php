<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DropWordPressTables extends Command
{
    protected $signature = 'db:drop-wordpress';
    protected $description = 'Drop all WordPress tables (mRWO2x_*)';

    public function handle()
    {
        $this->info('Searching for WordPress tables...');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        try {
            $tables = DB::select("SHOW TABLES LIKE 'mRWO2x_%'");
            
            if (empty($tables)) {
                $this->info('✓ No WordPress tables found!');
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
                return 0;
            }
            
            $this->info('Found ' . count($tables) . ' WordPress tables');
            $this->newLine();
            
            $bar = $this->output->createProgressBar(count($tables));
            $bar->start();
            
            foreach ($tables as $table) {
                $tableName = array_values((array)$table)[0];
                
                try {
                    DB::statement("DROP TABLE IF EXISTS `{$tableName}`");
                    $bar->advance();
                } catch (\Exception $e) {
                    $this->newLine();
                    $this->error("Failed to drop {$tableName}: " . $e->getMessage());
                }
            }
            
            $bar->finish();
            $this->newLine(2);
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            return 1;
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $this->info('✓ All WordPress tables removed successfully!');
        $this->newLine();
        
        // Show remaining tables
        $remaining = DB::select('SHOW TABLES');
        $this->info('Remaining tables: ' . count($remaining));
        
        foreach ($remaining as $table) {
            $tableName = array_values((array)$table)[0];
            $this->line('  - ' . $tableName);
        }
        
        return 0;
    }
}
