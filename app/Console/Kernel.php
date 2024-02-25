<?php

namespace App\Console;

Use Illuminate\Console\Scheduling\Schedule;
Use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //'App\Console\Commands\TelescopeClear',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        //$schedule->command('test:script')->everyMinute();

        //$schedule->command('telescope:prune')->hourly();

        //$schedule->command('backup:clean')->daily()->at('01:00');
        //$schedule->command('backup:run')->daily()->at('02:00');

        $schedule->command('queue:restart')->everyMinute();
        $schedule->command('queue:retry all')->everyFifteenMinutes();
        $schedule->command('queue:work --queue=adviseremails,clientemails --timeout=60 --sleep=5 --tries=3')->withoutOverlapping()->everyMinute();

        $schedule->command('portal_cache:calendars --account_id=1 --base_user_email=sam@tmblgroup.co.uk --weeks=4')->withoutOverlapping()->everyThirtyMinutes();

        //$schedule->command('inspire')->hourly();
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
