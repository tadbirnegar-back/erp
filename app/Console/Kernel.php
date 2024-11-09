<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // Start the queue worker at 00:05 (runs in the background)
        $schedule->command('queue:work --sleep=3 --tries=3')
            ->dailyAt('00:05')
            ->runInBackground()
            ->withoutOverlapping();

        // Stop the queue worker at 00:10 (using pkill to terminate the process)
        $schedule->exec('pkill -f "php artisan queue:work"')
            ->dailyAt('00:20')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
