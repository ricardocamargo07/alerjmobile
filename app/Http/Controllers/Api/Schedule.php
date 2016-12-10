<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Scrapers\Scraper as ScheduleScraper;

class Schedule extends Controller
{
	private $schedule;

	public function __construct(ScheduleScraper $schedule)
	{
		$this->schedule = $schedule;
	}

	public function all()
	{
		return $this->response($this->schedule->all());
	}

	public function item($item)
	{
		return $this->response(
			$this->removeUneededLinks(
				$this->schedule->getDocument($item)
			)
		);
	}
}
