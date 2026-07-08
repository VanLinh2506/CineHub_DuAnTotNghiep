<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Screen;

class FixScreenLayouts extends Command
{
    protected $signature = 'screens:fix-layouts';
    protected $description = 'Fix screen seat layouts to match booking logic (12 seats, J row is couple)';

    public function handle()
    {
        $this->info('Fixing screen layouts...');
        
        $screens = Screen::all();
        
        foreach ($screens as $screen) {
            $correctLayout = [
                'rows' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'],
                'cols' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                'vip_rows' => ['D', 'E', 'F'],
                'couple_rows' => ['J'],
                'layout_type' => 'standard'
            ];
            
            // Update without timestamps
            Screen::where('id', $screen->id)->update([
                'seat_layout_config' => json_encode($correctLayout),
                'total_seats' => 144
            ]);
            
            $this->info("Fixed: {$screen->screen_name} - 144 seats");
        }
        
        $this->info("✓ Fixed {$screens->count()} screens");
        
        return 0;
    }
}
