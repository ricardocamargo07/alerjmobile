<?php

namespace App\Services\Scrapers;

use Cache;
use App\Services\HttpClient;
use App\Data\Models\BillProject;
use Illuminate\Support\Debug\Dumper;
use App\Data\Models\BillProject as BillProjectModel;
use App\Services\Scrapers\DocumentPage as DocumentPageScraper;

/*
 * Projects: http://apiportal.alerj.rj.gov.br/api/v1.0/bill-projects
 */

class BillProjects
{
    private $client;

    private $documentPageScraper;

    private $projectUrls = [
        '2019 a 2023' => [
            'url' => 'http://alerjln1.alerj.rj.gov.br/scpro1923.nsf/',
            'section' => 'f4b46b3cdbba990083256cc900746cf6',
            'portal_id' => '144',
        ],

        '2015 a 2019' => [
            'url' => 'http://alerjln1.alerj.rj.gov.br/scpro1519.nsf/',
            'section' => '18c1dd68f96be3e7832566ec0018d833',
            'portal_id' => '7',
        ],
        '2011 a 2015' => [
            'url' => 'http://alerjln1.alerj.rj.gov.br/scpro1115.nsf/',
            'section' => '18c1dd68f96be3e7832566ec0018d833',
            'portal_id' => '22',
        ],
        '2007 a 2011' => [
            'url' => 'http://alerjln1.alerj.rj.gov.br/scpro0711.nsf/',
            'section' => '18c1dd68f96be3e7832566ec0018d833',
            'portal_id' => '37',
        ],

        //        '2003 a 2007' => [
        //            'url' => 'http://alerjln1.alerj.rj.gov.br/scpro0307.nsf/',
        //            'section' => '18c1dd68f96be3e7832566ec0018d833',
        //        ],
        //
        //        '1999 a 2003' => [
        //            'url' => 'http://alerjln1.alerj.rj.gov.br/scpro99.nsf/',
        //            'section' => '57b07275a3e4c007832567040007cc4d',
        //        ],
        //
        //        '1995 a 1998' => [
        //            'url' => 'http://alerjln1.alerj.rj.gov.br/scpro.nsf/',
        //            'section' => 'c9dcded42b23ccd3032566080049be12',
        //        ],
        //
        //        '1991 a 1994' => [
        //            'url' => 'http://alerjln1.alerj.rj.gov.br/scpro91_94.nsf/',
        //            'section' => 'cd311cb74f06d78f032566c80080a014',
        //        ],
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

    private function comment($string)
    {
        if ($this->command) {
            $this->command->comment($string);
        }
    }

    private function databaseIsEmpty()
    {
        return BillProjectModel::count() == 0;
    }

    private function extractAuthors($autors)
    {
        if (isset($autors['text'])) {
            return $autors['text'][0];
        }

        return collect($autors['textlist']['text'])
            ->flatten()
            ->implode(' / ');
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

    private function makeJsonQuery($level)
    {
        return sprintf($this->jsonQuery, $level);
    }

    private function makeSiteUrl($url, $portalId)
    {
        preg_match('|.*alerjln1.alerj.rj.gov.br(.*)|', $url, $matches);

        return "http://www3.alerj.rj.gov.br/lotus_notes/default.asp?id={$portalId}&url=" .
            base64_encode($matches[1] . '?OpenDocument&ExpandView');
    }

    /**
     * @param $items
     * @param $legislature
     * @return array
     */
    private function parseProject($items, $legislature)
    {
        $item = $items['viewentry'][0];

        $project = [];

        $project['code'] = $code = $item['@unid'];

        $project['authors'] = $this->extractAuthors($item['entrydata'][4]);

        $project['description'] = $description =
            $item['entrydata'][2]['text'][0];

        $project['date'] = isset($item['entrydata'][3]['datetime'])
            ? $item['entrydata'][3]['datetime'][0]
            : $item['@timestamp'];

        $project['url'] = $url =
            $legislature['url'] . $legislature['section'] . '/' . $code;

        $project['site_url'] = $this->makeSiteUrl(
            $url,
            $legislature['portal_id']
        );

        $project =
            $project + $this->extractProjectNumberFromDescription($description);

        $model = $this->addToDatabase($project);

        return [$project, $model];
    }

    public function scrape()
    {
        $this->comment('Starting phase 1...');

        $recordCount = 0;

        $toTheEnd = $this->databaseIsEmpty();

        foreach ($this->projectUrls as $years => $legislature) {
            $counter = 1;

            $this->comment($years);

            while (true) {
                if ($recordCount++ % 100 === 0) {
                    $this->comment($recordCount);
                }

                $items = $this->client->getArray(
                    $legislature['url'] . $this->makeJsonQuery($counter++)
                );

                if (!isset($items['viewentry'])) {
                    break;
                }

                try {
                    list($project, $model) = $this->parseProject(
                        $items,
                        $legislature
                    );
                } catch (\Exception $exception) {
                    $this->comment($exception->getMessage());

                    dump($project, $items);

                    throw $exception;
                }

                if (!$model->wasRecentlyCreated && !$toTheEnd) {
                    break;
                }
            }
        }
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
}
