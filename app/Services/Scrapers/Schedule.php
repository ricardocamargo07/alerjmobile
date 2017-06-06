<?php

namespace App\Services\Scrapers;

use Cache;
use Carbon\Carbon;
use App\Services\HttpClient;
use Illuminate\Support\Debug\Dumper;
use App\Data\Models\Schedule as ScheduleModel;
use App\Services\Scrapers\DocumentPage as DocumentPageScraper;

class Schedule
{
	private $client;

	private $documentPageScraper;

	private $scheduleUrl = 'http://alerjln1.alerj.rj.gov.br/ordemdia.nsf/c97e97dc1275d92503256bac007254ea';

	public function __construct(HttpClient $client, DocumentPageScraper $documentPageScraper)
	{
		$this->client = $client;

		$this->documentPageScraper = $documentPageScraper;
	}

    private function addToDatabase($entry) {
        if (! $schedule = ScheduleModel::where('alerj_id', $entry['alerj_id'])->first()) {
            $schedule = new ScheduleModel();
        }

        $schedule->alerj_id = $entry['alerj_id'];
        $schedule->title = $entry['title'];
        $schedule->document = $this->scrapeItem($entry['alerj_id']);
        $schedule->datetime = $entry['datetime'];

        $schedule->save();
    }

    public function all()
    {
        if ($schedule = Cache::get($cacheKey = 'allSchedule'))
        {
            return $schedule;
        }

        $result = ScheduleModel::orderBy('datetime', 'desc')->get();

        foreach ($result as $index => $item)
        {
            $result[$index]['carbon'] = [
                'date' => $result[$index]['datetime'],
                'timezone_type' => 3,
                'timezone' => 'UTC',
            ];
        }

        Cache::put($cacheKey, $result, 1);

        return $result;
    }

    public function getDocument($item)
    {
        if ($schedule = Cache::get($cacheKey = 'getDocument-'.$item))
        {
            return $schedule;
        }

        if ( ! $schedule = ScheduleModel::where('alerj_id', $item)->first())
        {
            return null;
        }

        Cache::put($cacheKey, $schedule->document, 1);

        return $schedule->document;
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
                $date = $item['entrydata'][0]['datetime'][0];

                if (isset($item['entrydata'][1]['datetime'][0]))
                {
                    $time = $item['entrydata'][1]['datetime'][0];
                }
                else
                {
                    $time = 'T000000,00';

                    if ($item['entrydata'][1]['text'][0])
                    {
                        $time = $item['entrydata'][1]['text'][0];
                    }
                }

				$appointment['alerj_id'] = $item['@unid'];
				$appointment['datetime'] = $this->toDateTime($date . $time);
				$appointment['title'] =  'Dia ' . (int) trim($appointment['datetime']->format('d')) . ' - ' . trim($item['entrydata'][3]['text'][0]) . ' (' . trim($appointment['datetime']->format('H\hi')) . ')';
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

    /**
     * Scrape schedule and store in the database.
     *
     * @param \App\Console\Commands\Schedule $command
     */
    public function scrapeToDatabase($command = null)
    {
        foreach($this->scrape() as $entry)
        {
            if (isset($entry['alerj_id']))
            {
                $this->addToDatabase($entry);

                if ($command)
                {
                    $command->comment($entry['alerj_id'] . ' - ' . $entry['title']);
                }
            }
        }
    }

    public function dd() {
        array_map(function($x) { (new Dumper)->dump($x); }, func_get_args());
    }
}
