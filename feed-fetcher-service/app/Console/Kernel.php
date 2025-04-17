<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\FetchFeedCommand;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        FetchFeedCommand::class,
    ];

    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(FetchFeedCommand::class)
            ->everyMinute() // Or everyFiveMinutes(), hourly(), etc.
            ->withoutOverlapping()
            ->onFailure(function () {
                // You can log to a custom channel or notify
                logger()->error('FetchFeedCommand failed.');
            });
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}