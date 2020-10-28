<?php

namespace App\Console;

use App\Console\Commands\TechnicalServiceFirstInspection;
use App\Console\Commands\TechnicalServiceYearlyInspection;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Class Kernel
 *
 * @package App\Console
 */
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        TechnicalServiceFirstInspection::class,
        TechnicalServiceYearlyInspection::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $logFile = storage_path('logs/schedule/app.log');

        $schedule->command('technicalService:first-inspection')->dailyAt('07:00')->appendOutputTo($logFile);

        $schedule->command('technicalService:yearly-inspection')->dailyAt('07:00')->appendOutputTo($logFile);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
