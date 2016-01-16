<?php namespace App\Console;

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
		'App\Console\Commands\Scrape',
		'App\Console\Commands\StressTest',
        'App\Console\Commands\Schedule',
        'App\Console\Commands\Congressmen',
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
//        $schedule->command('alerj:congressmen')->hourly()->withoutOverlapping();
		$schedule->command('alerj:scrape')->hourly()->withoutOverlapping();
        $schedule->command('alerj:schedule')->cron('* * * * * *')->withoutOverlapping();
	}
}
