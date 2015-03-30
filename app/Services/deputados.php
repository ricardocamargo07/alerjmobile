<?php

namespace App\Services;

use App\Services\Locator;
use Exception;
use RemoteWebDriver;
use Symfony\Component\DomCrawler\Crawler;
use WebDriverBy;
use WebDriverCapabilityType;
use WebDriverDimension;
use WebDriverExpectedCondition;

$driver = new webDriver();

$driver->get('http://www.alerj.rj.gov.br/deputados/center_dep_busca.asp');

$driver->takeScreenshot('/var/www/alerj/mobile/screen2.png');

sleep(2);

$driver->executeScript("document.querySelector('select[name=\"partido\"] option[value=\"TODOS\"]').selected = 'selected';");
$driver->executeScript('document.busca.submit();');

sleep(2);

$crawler = new Crawler($driver->getPageSource());

$crawler = $crawler->filter('html > body > div > table > tbody > tr > td > table > tbody > tr > td > div')->nextAll();

$crawler = $crawler->filter('table > tbody > tr');

$parties = [];

$lines = [];

foreach ($crawler as $row)
{
	$cells = $row -> getElementsByTagName('td');

	foreach ($cells as $cell)
	{
		if ($cell->nodeValue && strlen($cell->nodeValue) < 1000)
		{
			$lines[] = $cell->nodeValue;
		}
	}
}

foreach ($lines as $line)
{
	if (strpos($line, $selector = chr(194).chr(160).'- ') > 0 && strpos($line, 'Bancada') > 0)
	{
		$party = explode($selector, $line);

		$leader = trim($party[1]);
		$party = trim($party[0]);

//		for($counter = 0; $counter < strlen($party); $counter++)
//		{
//			echo $party[$counter] . ' = ' . ord($party[$counter]) . "\n";
//		}
//
//		die;
//		echo "Party Changed: $party \n";

		$parties[$party] = ['name' => $party, 'leader' => $leader, 'members' => []];
	}
	elseif ($line == 'Deputado' || $line == 'Email')
	{
		// ignore
	}
	elseif (strpos($line, '@') == false)
	{
		$member = ['name' => $line];
	}
	elseif (strpos($line, '@') !== false)
	{
		$member['email'] = $line;

		$parties[$party]['members'][] = $member;
	}
}

dd($parties);

$driver->quit();

class webDriver {

	private $driver;

	private $host = '127.0.0.1:4444';

	private $agent = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:25.0) Gecko/20100101 Firefox/25.0';

	private $browser = 'phantomjs';

	public function __construct()
	{
		$this->driver = RemoteWebDriver::create($this->host, $this->getCapabilities(), 5000);

		$window = new WebDriverDimension(1024, 768);

		$this->driver->manage()->window()->setSize($window);
	}

	function waitForText($text, $timeout = 10, $selector = null)
	{
		if (!$selector)
		{
			$condition = WebDriverExpectedCondition::textToBePresentInElement(WebDriverBy::xpath('//body'), $text);
			$this->driver->wait($timeout)->until($condition);
			return;
		}

		$condition = WebDriverExpectedCondition::textToBePresentInElement($this->getLocator($selector), $text);
		$this->driver->wait($timeout)->until($condition);
	}

	public function get($url)
	{
		return $this->driver->get($url);
	}

	public function __call($name, $args)
	{
		return call_user_func_array([$this->driver, $name], $args);
	}

	/**
	 * @return array
	 */
	private function getCapabilities()
	{
		return [
			WebDriverCapabilityType::BROWSER_NAME => $this->browser,
			$this->browser . '.page.settings.userAgent' => $this->agent,
		];
	}

	protected function getLocator($selector)
	{
		if ($selector instanceof WebDriverBy) {
			return $selector;
		}
		if (is_array($selector)) {
			return $this->getStrictLocator($selector);
		}
		if (Locator::isID($selector)) {
			return WebDriverBy::id(substr($selector, 1));
		}
		if (Locator::isCSS($selector)) {
			return WebDriverBy::cssSelector($selector);
		}
		if (Locator::isXPath($selector)) {
			return WebDriverBy::xpath($selector);
		}
		throw new Exception("Only CSS or XPath allowed");
	}

	protected function getStrictLocator(array $by)
	{
		$type = key($by);
		$locator = $by[$type];
		switch ($type) {
			case 'id':
				return WebDriverBy::id($locator);
			case 'name':
				return WebDriverBy::name($locator);
			case 'css':
				return WebDriverBy::cssSelector($locator);
			case 'xpath':
				return WebDriverBy::xpath($locator);
			case 'link':
				return WebDriverBy::linkText($locator);
			case 'class':
				return WebDriverBy::className($locator);
			default:
				throw new TestRuntime(
					"Locator type '$by' is not defined. Use either: xpath, css, id, link, class, name"
				);
		}
	}

	public function __get($name)
	{
		return $this->driver->{$name};
	}

}
