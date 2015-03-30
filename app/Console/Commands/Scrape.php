<?php

namespace App\Console\Commands;

use DB;
use App\Congressman;
use App\Party;
use App\Services\Scraper;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

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

		foreach ($scraper->scrapeCongressmen() as $party)
		{
			$model = Party::firstOrCreate(array_except($party, 'members'));

			foreach ($party['members'] as $congressman)
			{
				Congressman::firstOrCreate(array_merge(['party_id' => $model->id], $congressman));
			}
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
