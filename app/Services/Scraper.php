<?php

namespace App\Services;

use App\Services\Locator;
use Symfony\Component\DomCrawler\Crawler;

class Scraper {

	public function scrapeCongressmen()
	{
		$driver = new WebDriver();

		$driver->get('http://www.alerj.rj.gov.br/deputados/center_dep_busca.asp');

		sleep(3);

		$driver->executeScript("document.querySelector('select[name=\"partido\"] option[value=\"TODOS\"]').selected = 'selected';");
		$driver->executeScript('document.busca.submit();');

		sleep(4);

		$crawler = new Crawler($driver->getPageSource());

		$crawler = $crawler->filter('html > body > div > table > tbody > tr > td > table > tbody > tr > td > div')->nextAll();

		$crawler = $crawler->filter('table > tbody > tr');

		$parties = [];

		$lines = [];

		$nodes = [];

		foreach ($crawler as $row)
		{
			$cells = $row -> getElementsByTagName('td');

			foreach ($cells as $cell)
			{
				if ($cell->nodeValue && strlen($cell->nodeValue) < 1000)
				{
					$nodes[] = $cell;

					$lines[] = $cell->nodeValue;

					if ($a = $cell->getElementsByTagName("a"))
					{
						foreach($a as $node)
						{
							if ($href = $node->getAttribute('href'))
							{
								$lines[] = $href;
							}
						}
					}
				}
			}
		}

		foreach ($lines as $key => $line)
		{
			if (strpos($line, $selector = chr(194).chr(160).'- ') > 0 && strpos($line, 'Bancada') > 0)
			{
				$party = explode($selector, $line);

				$leader = trim($party[1]);
				$party = trim($party[0]);

				$parties[$party] = ['name' => $party, 'leader' => $leader, 'members' => []];
			}
			elseif ($line == 'Deputado' || $line == 'Email')
			{
				// ignore
			}
			elseif (strpos($line, 'javascript') !== false)
			{
				preg_match('#\((([^()]+|(?R))*)\)#', $line, $matches);

				if (count($matches) > 1)
				{
					$member['url'] = 'http://www.alerj.rj.gov.br/common/deputado.asp?codigo='.$matches[1];
					$member['alerj_id'] = $matches[1];
				}
			}
			elseif (strpos($line, '@') == false)
			{
				$member = ['name' => $line];
			}
			elseif (strpos($line, '@') !== false)
			{
				$member['email'] = str_replace('mailto:', '', $line);

				$parties[$party]['members'][$member['name']] = $member;
			}
		}

		$driver->quit();

		return $this->convertAccents($parties);
	}

	private function convertAccents($parties)
	{
		array_walk_recursive($parties, function (&$item, $key)
		{
			$item = str_replace('Ã£', 'ã', $item);
			$item = str_replace('Ã©', 'é', $item);

			$item = str_replace('Ã¡', 'á', $item);
			$item = str_replace('Ã¢', 'â', $item);
			$item = str_replace('Ã�', 'Á', $item);
		});

		return $parties;
	}

}

