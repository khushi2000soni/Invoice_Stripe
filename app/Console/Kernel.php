<?php

namespace App\Console;

use App\Console\Commands\BackupAndEmailCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected $commands = [
        // ... (other commands)
        BackupAndEmailCommand::class, // Add your custom command here
    ];

    protected function schedule(Schedule $schedule): void
    {
       $schedule->command('backup:email')->cron('0 0 31 12 *');
       // $schedule->command('backup:email')->everyTwoMinutes();
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
