<?php

namespace App\Console\Commands;

use App\Services\ScheduleScraper;
use Illuminate\Console\Command;

class Schedule extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'alerj:schedule';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Scrape the schedule from alerj.gov.br.';

    /**
     * Create a new command instance.
     *
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
		$this->info('Scraping schedule...');
		$scraper->scrapeToDatabase($this);
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
