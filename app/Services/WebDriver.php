<?php

namespace App\Services;

use Exception;
use WebDriverBy;
use RemoteWebDriver;
use WebDriverDimension;
use App\Services\Locator;
use WebDriverCapabilityType;
use WebDriverExpectedCondition;

class WebDriver {

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
