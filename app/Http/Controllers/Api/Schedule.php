<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Scrapers\Schedule as ScheduleScraper;
use App\Services\Scrapers\Discourse as DiscourseScraper;

class Schedule extends Controller
{
	private $scheduleScraper;
    /**
     * @var DiscourseScraper
     */
    private $discourseScraper;

    public function __construct(ScheduleScraper $schedule, DiscourseScraper $discourseScraper)
	{
		$this->scheduleScraper = $schedule;

        $this->discourseScraper = $discourseScraper;
    }

	public function all()
	{
        $data = collect($this->scheduleScraper->all()->toArray())
                    ->merge(collect($this->discourseScraper->all()->toArray()))
                    ->sortByDesc('datetime')
                    ->values();

		return $this->response($data);
	}

	public function item($item)
	{
	    if (! $document = $this->scheduleScraper->getDocument($item))
        {
            $document = $this->discourseScraper->getDocument($item);
        }

		return $this->response(
			$this->removeUneededLinks($document)
		);
	}
}
