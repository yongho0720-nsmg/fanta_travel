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
        Commands\CrawlerCommand::class,
        Commands\CrawlerAllCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     *
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('crawler:all')->timezone('asia/seoul')->hourly();

        //크롤링 체크
        $schedule->command('crawler:check')->timezone('asia/seoul')->hourlyAt(10);

        //push
        //개별 발송
        $schedule->command('push:worker P')
            ->everyMinute();

        // 전체 발송
        $schedule->command('push:worker A')
            ->everyMinute()
            // 중복 실행 방지
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
