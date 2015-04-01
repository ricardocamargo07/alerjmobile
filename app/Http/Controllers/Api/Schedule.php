<?php

namespace App\Http\Controllers\Api;

use App\Services\ScheduleScraper;
use App\Http\Controllers\Controller;

class Schedule extends Controller {

	private $schedule;

	public function __construct(ScheduleScraper $schedule)
	{
		$this->schedule = $schedule;
	}

	public function all()
	{
		return $this->schedule->scrape();
	}

	public function item($item)
	{
		return $this->schedule->scrapeItem($item);
	}

}
