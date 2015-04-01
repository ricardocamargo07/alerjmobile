<?php

namespace App\Services;

use App\Services\HttpClient;
use Symfony\Component\DomCrawler\Crawler;

class DocumentPageScraper {

	private $client;

	public function __construct(HttpClient $client)
	{
		$this->client = $client;
	}

	public function scrape($base_url, $item)
	{
		$url = "$base_url/$item?OpenDocument";

		$page = $this->client->getRaw($url);

		$crawler = new Crawler($page);

		$crawler = $crawler->filter('body');

		return $crawler->html();
	}

}
