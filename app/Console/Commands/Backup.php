<?php

namespace App\Console\Commands;

use App\Services\DownloadFromPortal;
use Illuminate\Console\Command;

class Backup extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'alerj:backuportal';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Backup www.alerj.rj.gov.br';

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
	public function fire(DownloadFromPortal $downloader)
	{
		$this->info('Backuping...');

        $downloader->execute();

        $this->comment('Done.');
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
