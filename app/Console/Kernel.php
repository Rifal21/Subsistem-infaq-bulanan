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
        // $schedule->command('app:kirim-notifikasi-s-p-p-auto')->dailyAt('08:00');
        // $schedule->command('app:kirim-notifikasi-s-p-p-auto --bulan=OKT')->hourly();
        $schedule->command('spp:kirim-wa --bulan=OKT')->hourly();
        // $schedule->command('spp:kirim-wa --bulan=OKT')->everyMinute();
        // $schedule->command('spp:kirim-wa --bulan=OKT')->everyFiveMinutes();
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
