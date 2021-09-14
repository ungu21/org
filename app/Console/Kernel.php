<?php

namespace App\Console;

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
        
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('saldo:cek')->everyMinute();
        $schedule->command('transaksi:cek')->everyMinute();
        
        $schedule->command('deposit:cek')->everyFiveMinutes();
        
        $schedule->command('kategoripembelian:update')->twiceDaily(0, 12);
        $schedule->command('kategoripembayaran:update')->twiceDaily(0, 12);
        
        $schedule->command('operatorpembelian:update')->hourly();
        $schedule->command('operatorpembayaran:update')->hourly();
        
        $schedule->command('produkpembelian:update')->everyTenMinutes();
        $schedule->command('produkpembayaran:update')->everyTenMinutes();
    }
    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        
        require base_path('routes/console.php');
    }
}
