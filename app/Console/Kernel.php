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
        // ZONA PROMAX HUB Data Updates - Every 30 seconds
        $schedule->command('zona:update-data')
                ->everyThirtySeconds()
                ->withoutOverlapping()
                ->runInBackground();

        // Auto-update RTP games, tools, and hot_and_fresh with random values - Every 30 minutes
        $schedule->command('data:update-random')
                ->everyThirtyMinutes()
                ->withoutOverlapping()
                ->runInBackground();

        // Add new testimonials - Every hour
        $schedule->command('testimonials:add-new')
                ->everyThreeHours()
                ->withoutOverlapping()
                ->runInBackground();
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
