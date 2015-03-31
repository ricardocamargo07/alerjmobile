<?php

namespace App\Services;

use App\Party;
use App\Regiment;
use App\Congressman;
use App\Services\Locator;
use Symfony\Component\DomCrawler\Crawler;

class Scraper {

	public function __construct()
	{
		$this->driver = new WebDriver();
	}

	public function scrapeCongressmen()
	{
		foreach ($this->getCongressmen() as $party)
		{
			$model = Party::firstOrCreate(array_except($party, 'members'));

			foreach ($party['members'] as $congressman)
			{
				Congressman::firstOrCreate(array_merge(['party_id' => $model->id], $congressman));
			}
		}
	}

	public function getCongressmen()
	{
		$this->driver->get('http://www.alerj.rj.gov.br/deputados/center_dep_busca.asp');

		sleep(3);

		$this->driver->executeScript("document.querySelector('select[name=\"partido\"] option[value=\"TODOS\"]').selected = 'selected';");
		$this->driver->executeScript('document.busca.submit();');

		sleep(4);

		$crawler = new Crawler($this->driver->getPageSource());

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

		foreach ($parties as $party_id => $party)
		{
			foreach ($party['members'] as $member_id => $member)
			{
				$parties[$party_id]['members'][$member_id]['page'] = $this->scrapeProfilePage($member);
			}
		}

		$this->driver->quit();

		return $this->arrayConvertAccents($parties);
	}

	private function arrayConvertAccents($parties)
	{
		array_walk_recursive($parties, function (&$item, $key)
		{
			$item = $this->convertAccents($item);
		});

		return $parties;
	}

	function convertAccents($item)
	{
		$item = str_replace('Ã£', 'ã', $item);
		$item = str_replace('Ã©', 'é', $item);

		$item = str_replace('Ã¡', 'á', $item);
		$item = str_replace('Ã¢', 'â', $item);
		$item = str_replace('Ã�', 'Á', $item);

		$item = str_replace('Ãª', 'ê', $item);

		return $item;
	}

	private function scrapeProfilePage($member)
	{
		echo "Scraping page for {$member['name']} \n";

		$html = file_get_contents($member['url']);
		$html = str_replace(chr(13), '', $html);
		$html = str_replace(chr(10), '', $html);

		$html = substr($html, strpos($html, '<table border=0 vspace=0 hspace=0 cellspacing=0 cellpadding=0>'));
		$html = substr($html, 0, strpos($html, '</table>') + 8);

		$html = str_replace("<img src='../imagens", "<img src='http://www.alerj.rj.gov.br/imagens", $html);

		$html = str_replace('width="499"', "", $html);
		$html = str_replace('width=499', "", $html);
		$html = str_replace('width="15"', "", $html);
		$html = str_replace('width=15', "", $html);
		$html = str_replace('<td colspan=3 bgcolor=#FFFFFF align=center>', '<td>&nbsp;</td><td colspan=3 bgcolor=#FFFFFF align=center>', $html);
		$html = str_replace('align=center', '', $html);
		$html = str_replace('nowrap', '', $html);

		return $this->convertAccents(utf8_encode("<table>$html</table>"));
	}

	public function scrapeRegiment()
	{
		$regiment = json_decode(file_get_contents('http://alerjln1.alerj.rj.gov.br/regiment2.nsf/e975dc081da5ea8c032568f5006d4467/a9574763868365930325682b007d9a41?readviewentries&outputformat=json&Count=1000'), true);

		$regiment = $regiment['viewentry'];

		foreach ($regiment as $item)
		{
			$data = [];

			$data['position'] = $item['@position'];

			$data['level'] = substr_count($data['position'], '.');

			if (isset($item['@unid']))
			{
				$data['document_id'] = $item['@unid'];

				$data['page'] = $this->scrapeRegimentPage($item['@unid']);

				$data['title'] = $item['entrydata'][3]['text'][0];
			}
			else
			{
				$data['title'] = $item['entrydata'][0]['text'][0];
			}

			echo $data['title']."\n";

			Regiment::create($data);
		}
	}

	private function scrapeRegimentPage($item)
	{
		$url = "http://alerjln1.alerj.rj.gov.br/regiment2.nsf/e975dc081da5ea8c032568f5006d4467/$item?OpenDocument";

		$page = file_get_contents($url);

		$crawler = new Crawler($page);

		$crawler = $crawler->filter('body');

		dd($crawler->html());
	}

}
