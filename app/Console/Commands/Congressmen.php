<?php

namespace App\Console\Commands;

use DB;
use App\Services\Scraper;
use Illuminate\Console\Command;

class Congressmen extends Command
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'alerj:congressmen';

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
     * @param Scraper $scraper
     * @return mixed
     */
	public function fire(Scraper $scraper)
	{
        $this->info('Scraping congressmen...');

        DB::transaction(function() use ($scraper)
        {
            DB::table('parties')->delete();

            $scraper->scrapeCongressmen();
        });
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
