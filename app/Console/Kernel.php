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
        Commands\UpdateApplicationAccessToken::class,
        Commands\UpdateClientAccessToken::class,
        Commands\CreateApplicationUser::class,
        Commands\SignupUnRegisteredClient::class,
        Commands\GenerateBillAndDeActivatePlan::class,
        Commands\DeActivateClientPayableSubCategory::class,
        Commands\SendOfflineDueSms::class,
        Commands\DeleteClientEvents::class,
        Commands\DeleteCollegeEvents::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('updateapplicationaccesstoken:cron');
        $schedule->command('updateclientaccesstoken:cron');
        $schedule->command('createapplicationuser:command');
        $schedule->command('signupunregisteredclient:command');
        $schedule->command('generatebillanddeactivateplan:cron');
        $schedule->command('deactivateclientpayablesubcategory:cron');
        $schedule->command('sendofflineduesms:cron');
        $schedule->command('deleteclientevents:command');
        $schedule->command('deletecollegeevents:command');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}

