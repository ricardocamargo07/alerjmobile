<?php

namespace App\Console\Commands;

use DB;
use App\Services\Scraper;
use Illuminate\Console\Command;

class Scrape extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'alerj:scrape';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Scrape the hell out of alerj.com.br.';

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
	 * @return mixed
	 */
	public function fire(Scraper $scraper)
	{
		DB::table('parties')->delete();
		DB::table('regiment')->delete();

		$this->info('Scraping congressmen...');
		$scraper->scrapeCongressmen();

		$this->info('Scraping regiment...');
		$scraper->scrapeRegiment();
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
