<?php

namespace App\Services\Scrapers;

use App\Services\HttpClient;
use Symfony\Component\DomCrawler\Crawler;

class DocumentPage
{
	private $client;

	public function __construct(HttpClient $client)
	{
		$this->client = $client;
	}

    public function clearHtml($html) {
        $decoded = '';

        for ($i=0; $i < strlen($html); $i++)  {
            $char = substr($html,$i,1);

            if (ord($char) >= 32)
            {
                $decoded .= $char;
            }
        }

        $decoded = trim($decoded);

//        $decoded = $this->removeEmptyTable($decoded);

        return $decoded;
    }

    private function removeEmptyTable($decoded)
    {
        $string = '<table width="100%" border="1"><tr valign="top"><td width="100%"><img width="1" height="1" src="/icons/ecblank.gif" border="0" alt=""></td></tr></table>';

        return
            substr($decoded, 0, strpos($decoded, $string)) .
            substr($decoded, strpos($decoded, $string) + strlen($string))
        ;
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
