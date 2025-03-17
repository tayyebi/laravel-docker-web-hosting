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
        // Example: Schedule a command to run daily at midnight
        // $schedule->command('inspire')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        // If you need to add commands manually
        // $this->commands([
        //     \App\Console\Commands\ExampleCommand::class,
        // ]);
    }

    /**
     * The Artisan commands provided by the application.
     *
     * @var array
     */
    protected $commands = [
        
    ];
}
