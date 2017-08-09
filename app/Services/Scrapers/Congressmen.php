<?php

namespace App\Services\Scrapers;

use Cache;
use Carbon\Carbon;
use App\Services\HttpClient;
use Illuminate\Support\Debug\Dumper;
use App\Data\Models\Discourse as DiscourseModel;
use App\Services\Scrapers\DocumentPage as DocumentPageScraper;

/*
 * Deputados: apialerj.rj.gov.br/api/deputadoservice
 * Notícias: apialerj.rj.gov.br/api/noticiaservice
 * Agenda: apialerj.rj.gov.br/api/agendaservice
 */

class Discourse
{
	private $client;

	private $documentPageScraper;

	private $discourseUrl = 'http://apialerj.rj.gov.br/api/deputadoservice';

	private $jsonQuery = '?readviewentries&OutputFormat=json&Count=100&Expand=%s';

    private $command;

    public function __construct(HttpClient $client, DocumentPageScraper $documentPageScraper)
	{
		$this->client = $client;

		$this->documentPageScraper = $documentPageScraper;
	}

    private function addToDatabase($level0, $command = null)
    {
        $this->command = $command;

	    foreach ($level0['items'] as $level1) {
            foreach ($level1['items'] as $level2) {
                foreach ($level2['items'] as $entry) {
                    if (! $discourse = DiscourseModel::where('alerj_id', $entry['alerj_id'])->first()) {
                        $discourse = new DiscourseModel();
                    }

                    if ($discourse->document) {
                        continue;
                    }

                    $discourse->alerj_id = $entry['alerj_id'];
                    $discourse->document = $this->scrapeItem($entry['alerj_id']);
                    $discourse->session_type = $this->truncate($entry['session_type']);
                    $discourse->expedient_type = $this->truncate($entry['expedient_type']);
                    $discourse->document_type = $this->truncate($entry['document_type']);
                    $discourse->person = $this->truncate($this->makePerson($entry['person']), 4096);

                    $discourse->title = $discourse->document_type . ' - ' .
                        $discourse->person . ($discourse->expedient_type ? ' - ' : '') .
                        $discourse->expedient_type . ' - ' .
                        $discourse->session_type
                    ;

                    $discourse->datetime = $this->toDateTime($entry['date']);

                    $discourse->save();

                    $this->comment($discourse->datetime->format('Y-m-d') . ' - ' . $discourse->alerj_id . ' - ' . $discourse->title);
                }
            }
        }
    }

    public function all()
    {
        if ($discourse = Cache::get($cacheKey = 'allDiscourse'))
        {
            return $discourse;
        }

        $result = DiscourseModel::take(150)->orderBy('datetime', 'desc')->get();

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

    private function comment($string)
    {
        if ($this->command)
        {
            $this->command->comment($string);
        }
    }

    public function getDocument($item)
    {
        if ($discourse = Cache::get($cacheKey = 'getDocument-'.$item))
        {
            return $discourse;
        }

        if ( ! $discourse = DiscourseModel::where('alerj_id', $item)->first())
        {
            return null;
        }

        Cache::put($cacheKey, $discourse->document, 1);

        return $discourse->document;
    }

    private function getLevel($item)
    {
        return substr_count($item, '.') + 1;
    }

    private function makeJsonQuery($level)
    {
        return sprintf($this->jsonQuery, $level);
    }

    private function makePerson($person)
    {
        if (is_array($person)) {
            $person = implode(' / ', $person);
        }

        return $person;
    }

    public function scrape()
    {
        return Cache::remember('scrape-discourses', 60, function () {
            $discourse = [];
            $year = null;
            $yearLevel = null;
            $month = null;
            $monthLevel = null;

            $this->comment('Starting phase 1...');

            foreach (range(1,2) as $range) {
                $items = $this->client->getArray($this->discourseUrl . $this->makeJsonQuery($range));
                $items = $items['viewentry'];

                foreach($items as $item)
                {
                    if ($this->getLevel($item['@position']) == 1) {
                        $year = $item['entrydata'][0]['number'][0];
                        $yearLevel = $item['@position'];

                        if (! isset($discourse[$yearLevel])) {
                            $discourse[$yearLevel]['date'] = $year;
                            $discourse[$yearLevel]['items'] = [];
                        }
                    }

                    if ($this->getLevel($item['@position']) == 2) {
                        $month = $item['entrydata'][0]['text'][0];
                        $MonthLevel = $item['@position'];

                        if (! isset($discourse[$yearLevel][$MonthLevel])) {
                            $discourse[$yearLevel]['items'][$MonthLevel]['date'] = $month;
                            $discourse[$yearLevel]['items'][$MonthLevel]['items'] = [];
                        }
                    }
                }
            }

            $this->comment('Starting phase 2...');

            foreach ($discourse as $keyLevel1 => $dataLevel1) {
                foreach ($dataLevel1['items'] as $keyLevel2 => $dataLevel2) {
                    $items = $this->client->getArray($this->discourseUrl . $this->makeJsonQuery($keyLevel2));
                    $items = $items['viewentry'];

                    foreach ($items as $item) {
                        if ($this->getLevel($item['@position']) == 3) {
                            $day = $item['entrydata'][0]['text'][0];
                            $DayLevel = $item['@position'];

                            $discourse[$keyLevel1]['items'][$keyLevel2]['items'][$DayLevel]['date'] = $day;
                            $discourse[$keyLevel1]['items'][$keyLevel2]['items'][$DayLevel]['items'] = [];
                        }
                    }
                }
            }

            $this->comment('Starting phase 3...');

            foreach ($discourse as $keyLevel1 => $dataLevel1) {
                $dateLevel1 = $dataLevel1['date'];

                foreach ($dataLevel1['items'] as $keyLevel2 => $dataLevel2) {
                    $dateLevel2 = $dataLevel2['date'];

                    foreach ($dataLevel2['items'] as $keyLevel3 => $dataLevel3) {
                        $dateLevel3 = $dataLevel3['date'];

                        $items = $this->client->getArray($this->discourseUrl . $this->makeJsonQuery($keyLevel3));
                        $items = $items['viewentry'];

                        foreach ($items as $item) {
                            if ($this->getLevel($item['@position']) == 4) {
                                $document_id = $item['@unid'];
                                $session_type = $item['entrydata'][0]['text'][0];
                                $expedient_type = $item['entrydata'][1]['text'][0];
                                $document_type = $item['entrydata'][3]['text'][0];

                                if (isset($item['entrydata'][5]['textlist'])) {
                                    $person = [];

                                    foreach ($item['entrydata'][5]['textlist']['text'] as $textitem) {
                                        $person[] = $textitem[0];
                                    }
                                }
                                else {
                                    $person = $item['entrydata'][5]['text'][0];
                                }

                                $DocumentLevel = $item['@position'];

                                $discourse[ $keyLevel1 ]['items'][ $keyLevel2 ]['items'][ $keyLevel3 ]['items'][ $DocumentLevel ]['alerj_id'] = $document_id;
                                $discourse[ $keyLevel1 ]['items'][ $keyLevel2 ]['items'][ $keyLevel3 ]['items'][ $DocumentLevel ]['session_type'] = $session_type;
                                $discourse[ $keyLevel1 ]['items'][ $keyLevel2 ]['items'][ $keyLevel3 ]['items'][ $DocumentLevel ]['expedient_type'] = $expedient_type;
                                $discourse[ $keyLevel1 ]['items'][ $keyLevel2 ]['items'][ $keyLevel3 ]['items'][ $DocumentLevel ]['document_type'] = $document_type;
                                $discourse[ $keyLevel1 ]['items'][ $keyLevel2 ]['items'][ $keyLevel3 ]['items'][ $DocumentLevel ]['person'] = $person;
                                $discourse[ $keyLevel1 ]['items'][ $keyLevel2 ]['items'][ $keyLevel3 ]['items'][ $DocumentLevel ]['date'] = [$dateLevel1, $dateLevel2, $dateLevel3];
                            }
                        }
                    }
                }
            }

            return $discourse;
        });
    }

	private function toDateTime($date)
    {
        list($day, $month) = explode('/', $date[2]);

        return Carbon::create($date[0], $month, $day, 15, 0, 0);
    }


    public function scrapeItem($item)
	{
		return $this->documentPageScraper->scrape($this->discourseUrl, $item);
	}

    /**
     * Scrape discourse and store in the database.
     *
     * @param \App\Console\Commands\Discourse $command
     */
    public function scrapeToDatabase($command = null)
    {
        $this->command = $command;

        foreach($this->scrape() as $entry)
        {
            $this->addToDatabase($entry, $command);
        }
    }

    public function dd() {
        array_map(function($x) { (new Dumper)->dump($x); }, func_get_args());
    }

    private function truncate($string, $size = 250)
    {
        return strlen($string) > $size
                ? substr($string, 0, $size)."..."
                : $string
        ;
    }
}
