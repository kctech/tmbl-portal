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

        $schedule->command('leads:contact')->environments(['production'])->withoutOverlapping()->everyMinute();

        $schedule->command('mab:sync-users --refresh=true --branch-id=9d214e47-c839-4785-a415-d61b6beffc01')->environments(['production'])->withoutOverlapping()->daily()->at('05:00');

        $schedule->command('queue:restart')->environments(['production'])->everyMinute();
        $schedule->command('queue:retry all')->environments(['production'])->everyFifteenMinutes();
        $schedule->command('queue:work --queue=adviseremails,clientemails,lead_chase_steps --timeout=60 --sleep=5 --tries=3')->environments(['production'])->withoutOverlapping()->everyMinute();

        $schedule->command('portal_cache:calendars --account_id=1 --base_user_email=sam@tmblgroup.co.uk --weeks=4')->environments(['production'])->withoutOverlapping()->everyThirtyMinutes();

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
