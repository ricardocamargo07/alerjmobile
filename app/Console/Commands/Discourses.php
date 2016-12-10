<?php

namespace App\Console\Commands;

use App\Services\Scrapers\Discourse as DiscourseScraper;
use Illuminate\Console\Command;

class Discourses extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'alerj:discourses';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Scrape discourses from alerj.gov.br.';

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
     * @param DiscourseScraper $scraper
     * @return mixed
     */
	public function fire(DiscourseScraper $scraper)
	{
		$this->info('Scraping discourses...');

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
