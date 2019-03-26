<?php

namespace App\Services\Scrapers;

use Cache;
use Carbon\Carbon;
use App\Services\HttpClient;
use Illuminate\Support\Debug\Dumper;
use App\Data\Models\BillProject as BillProjectModel;
use App\Services\Scrapers\DocumentPage as DocumentPageScraper;

/*
 * Deputados: apialerj.rj.gov.br/api/deputadoservice
 * Notícias: apialerj.rj.gov.br/api/noticiaservice
 * Agenda: apialerj.rj.gov.br/api/agendaservice
 */

class BillProjects
{
    private $client;

    private $documentPageScraper;

    private $projectUrls = [
        '2019 a 2023' => [
            'url' => 'http://alerjln1.alerj.rj.gov.br/scpro1923.nsf/',
            'section' => 'f4b46b3cdbba990083256cc900746cf6',
        ],
    ];

    private $jsonQuery = 'vlei?readviewentries&OutputFormat=json&Expand=1&Count=1&Start=%s.1';

    private $command;

    public function __construct(
        HttpClient $client,
        DocumentPageScraper $documentPageScraper
    ) {
        $this->client = $client;

        $this->documentPageScraper = $documentPageScraper;
    }

    private function addToDatabase($project)
    {
        $model = BillProjectModel::where('code', $project['code'])->first();

        if (is_null($model)) {
            $model = BillProjectModel::create($project)->first();
        }

        return $model;
    }

    public function all()
    {
        if ($discourse = Cache::get($cacheKey = 'allDiscourse')) {
            return $discourse;
        }

        $result = DiscourseModel::take(150)
            ->orderBy('datetime', 'desc')
            ->get();

        foreach ($result as $index => $item) {
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
        if ($this->command) {
            $this->command->comment($string);
        }
    }

    private function extractProjectNumberFromDescription($description)
    {
        preg_match('|\-\s*(\d{4})(\d{2})(\d{5})\s*\-|', $description, $matches);

        return [
            'year' => $matches[1],
            'month' => $matches[2],
            'number' => $matches[3],
        ];
    }

    public function getDocument($item)
    {
        if ($discourse = Cache::get($cacheKey = 'getDocument-' . $item)) {
            return $discourse;
        }

        if (!($discourse = DiscourseModel::where('alerj_id', $item)->first())) {
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
        return Cache::remember('scrape-projects', 60, function () {
            $this->comment('Starting phase 1...');

            foreach ($this->projectUrls as $legislature) {
                $counter = 1;

                while (true) {
                    $items = $this->client->getArray(
                        $legislature['url'] . $this->makeJsonQuery($counter++)
                    );

                    if (!isset($items['viewentry'])) {
                        break;
                    }

                    $item = $items['viewentry'][0];

                    $project = [];

                    $project['code'] = $code = $item['@unid'];

                    $project['description'] = $description =
                        $item['entrydata'][2]['text'][0];

                    $project['date'] = $item['entrydata'][3]['datetime'][0];

                    $project['url'] =
                        $legislature['url'] .
                        $legislature['section'] .
                        '/' .
                        $code;

                    $project =
                        $project +
                        $this->extractProjectNumberFromDescription(
                            $description
                        );

                    $this->addToDatabase($project);
                }
            }
        });
    }

    private function toDateTime($date)
    {
        list($day, $month) = explode('/', $date[2]);

        return Carbon::create($date[0], $month, $day, 15, 0, 0);
    }

    public function scrapeItem($item)
    {
        return $this->documentPageScraper->scrape($this->projectUrls, $item);
    }

    /**
     * Scrape discourse and store in the database.
     *
     * @param \App\Console\Commands\Discourse $command
     */
    public function scrapeToDatabase($command = null)
    {
        $this->command = $command;

        foreach ($this->scrape() as $entry) {
            $this->addToDatabase($entry, $command);
        }
    }

    public function dd()
    {
        array_map(function ($x) {
            (new Dumper())->dump($x);
        }, func_get_args());
    }

    private function truncate($string, $size = 250)
    {
        return strlen($string) > $size
            ? substr($string, 0, $size) . '...'
            : $string;
    }
}
