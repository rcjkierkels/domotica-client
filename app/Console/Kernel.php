<?php

namespace App\Console;

use App\Console\Commands\NotifyServer;
use App\Console\Commands\RunTasks;
use App\Console\Commands\UpdateClient;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        NotifyServer::class,
        RunTasks::class,
        UpdateClient::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        /**
         * Run task watcher. This is a continues task and should only be stopped by an update
         */
        $schedule->command('client:run')
                  ->everyMinute()
                  ->withoutOverlapping();


    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
