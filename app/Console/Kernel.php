<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Generate weekly forecast setiap Minggu malam
        $schedule->command('lstm:predict --steps=7')->weeklyOn(7, '23:00');
        
        // Generate monthly forecast setiap tanggal 1
        $schedule->command('lstm:predict --steps=30')->monthlyOn(1, '00:00');
        
        // Cleanup logs lama setiap hari
        $schedule->command('model:prune')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
