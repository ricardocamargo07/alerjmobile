<?php

namespace App\Services;

use Symfony\Component\DomCrawler\Crawler;

class DocumentPageScraper
{
	private $client;

	public function __construct(HttpClient $client)
	{
		$this->client = $client;
	}

    private function clearHtml($html) {
        $decoded = '';

        for ($i=0; $i < strlen($html); $i++)  {
            $char = substr($html,$i,1);

            if (ord($char) >= 32)
            {
                $decoded .= $char;
            }
        }

        $decoded = trim($decoded);

        return $decoded;
    }

    public function scrape($base_url, $item)
	{
		$url = "$base_url/$item?OpenDocument";

		$page = $this->client->getRaw($url);

		$crawler = new Crawler($page);

		$crawler = $crawler->filter('body');

		return $this->clearHtml($crawler->html());
	}
}
