<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
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
	    $discourse = collect($this->discourseScraper->all()->toArray())->map(function($item) {
            $item['title'] = $item['title'].' (23h59)';

            return $item;
        });

	    $sorter = function($item) {
	        $carbon = new Carbon($item['carbon']['date']);
	        return $carbon->format('Ymd') . (isset($item['document_type']) ? 'A' : 'B') . $carbon->format('Hms');
        };

        $data = collect($this->scheduleScraper->all()->toArray())
                    ->merge($discourse)
                    ->sortByDesc($sorter)
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
