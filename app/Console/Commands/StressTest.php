<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;
use App\Services\Scrapers\Schedule as ScheduleScraper;

class StressTest extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'alerj:stress';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Stress the hell out of alerj.com.br.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

    /**
     * Execute the console command.
     *
     * @param ScheduleScraper $scraper
     * @return mixed
     */
	public function fire(ScheduleScraper $scraper)
	{
		$this->info('Stressing...');

		foreach (range(0, 1000) as $number)
		{
			$scraper->scrape();
			$this->error($number);
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [];
	}

}
