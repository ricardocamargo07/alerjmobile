<?php

namespace App\Services;

use Carbon\Carbon;

class ScheduleScraper
{
	private $client;

	private $documentPageScraper;

	private $scheduleUrl = 'http://alerjln1.alerj.rj.gov.br/ordemdia.nsf/c97e97dc1275d92503256bac007254ea';

	public function __construct(HttpClient $client, DocumentPageScraper $documentPageScraper)
	{
		$this->client = $client;

		$this->documentPageScraper = $documentPageScraper;
	}

	public function scrape()
	{
		$items = $this->client->getArray($this->scheduleUrl . '?readviewentries&outputformat=json&Count=30');

		$items = $items['viewentry'];

		$schedule = [];

		$year = 1900;

		foreach($items as $item)
		{
			$appointment = [];

			if ($item['entrydata'][0]['@name'] == '$7')
			{
				$year = $item['entrydata'][0]['number'][0];

				continue;
			}
			elseif ($item['entrydata'][0]['@name'] == '$5')
			{
				$appointment['title'] = $item['entrydata'][0]['text'][0].' de '.$year;
			}
			elseif (isset($item['@unid']) && isset($item['entrydata'][0]['datetime'][0]))
			{
				$appointment['alerj_id'] = $item['@unid'];
				$appointment['carbon'] = $this->toDateTime($item['entrydata'][0]['datetime'][0] . $item['entrydata'][1]['datetime'][0]);
				$appointment['title'] =  'Dia ' . (int) trim($appointment['carbon']->format('d')) . ' - ' . trim($item['entrydata'][3]['text'][0]) . ' (' . trim($appointment['carbon']->format('H\hi')) . ')';
			}

			$schedule[] = $appointment;
		}

		return $schedule;
	}

	private function toDateTime($timestamp)
	{
		return Carbon::createFromTimestamp(
			strtotime(
				substr($timestamp, 0, strpos($timestamp, ','))
			)
		);
	}

	public function scrapeItem($item)
	{
		return $this->documentPageScraper->scrape($this->scheduleUrl, $item);
	}
}
