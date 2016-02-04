<?php

namespace Joomla\Tests\Page;

use Codeception\Configuration;
use Codeception\Exception\ElementNotFound;
use Codeception\Module\JoomlaBrowser;
use Codeception\Util\Locator;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;
use Symfony\Component\DomCrawler\Crawler;

class Page
{
	/** @var PageFactory */
	protected $factory;

	/** @var JoomlaBrowser */
	protected $browser;

	/** @var  string */
	protected $url = '/';

	/** @var array  */
	protected $elements = [];

	public function __construct(PageFactory $factory, JoomlaBrowser $browser)
	{
		$this->factory = $factory;
		$this->browser = $browser;
	}

	/**
	 * Returns the URL of the page.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->url;
	}

	public function get($selector)
	{
		$method = 'get' . ucfirst($selector);
		if (method_exists($this, $method))
		{
			return $this->$method();
		}

		return $this->findElement($this->marshalSelector($selector))->getText();
	}

	public function set($selector, $value)
	{
		$method = 'set' . ucfirst($selector);
		if (method_exists($this, $method))
		{
			$this->$method($value);

			return;
		}
		$this->fillField($this->marshalSelector($selector), $value);
	}

	public function click($selector)
	{
		return $this->findElement($this->marshalSelector($selector))->click();
	}

	/**
	 * Determines, if this object represents the current page.
	 *
	 * @return boolean
	 */
	public function isCurrent()
	{
		$url = parse_url($this->browser->webDriver->getCurrentURL());

		return $url['path'] == $this->url;
	}

	/**
	 * Dumps the current page.
	 *
	 * Three files are generated:
	 *
	 *   - `<filename>.html`  The page's source code
	 *   - `<filename>.png`   A screenshot of the page
	 *   - `<filename>.url`   The actual URL of the page
	 *
	 * @param string $pageName The name of the page. Defaults to the page class.
	 *                         It is recommended to supply the `__METHOD__` constant.
	 *                         However, any string can be used.
	 *                         Only characters valid for identifiers are allowed.
	 *                         '::' is replaced with '.', any other special character than '.' is removed.
	 *
	 * @throws \Codeception\Exception\ConfigurationException
	 */
	public function dump($pageName = null)
	{
		if (is_null($pageName))
		{
			$pageName = get_class($this);
		}
		$pageName  = preg_replace(['~::~', '~[^\w\.]+~'], ['.', ''], $pageName);
		$outputDir = Configuration::outputDir();

		$browser = $this->browser->webDriver;
		$browser->takeScreenshot($outputDir . '/' . $pageName . '.png');
		file_put_contents($outputDir . '/' . $pageName . '.url', $browser->getCurrentURL());
		file_put_contents($outputDir . '/' . $pageName . '.html', $browser->getPageSource());
	}

	/**
	 * @param $selector
	 *
	 * @return WebDriverElement[]
	 * @throws \Codeception\Exception\ElementNotFound
	 */
	public function findFields($selector)
	{
		if ($selector instanceof WebDriverElement)
		{
			return [$selector];
		}
		if (is_array($selector) || ($selector instanceof WebDriverBy))
		{
			$fields = $this->findElements($selector);

			if (empty($fields))
			{
				throw new ElementNotFound($selector);
			}

			return $fields;
		}

		$locator = Crawler::xpathLiteral(trim($selector));
		// by text or label
		$xpath  = Locator::combine(
			".//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')][(((./@name = $locator) or ./@id = //label[contains(normalize-space(string(.)), $locator)]/@for) or ./@placeholder = $locator)]",
			".//label[contains(normalize-space(string(.)), $locator)]//.//*[self::input | self::textarea | self::select][not(./@type = 'submit' or ./@type = 'image' or ./@type = 'hidden')]"
		);
		$fields = $this->browser->webDriver->findElements(WebDriverBy::xpath($xpath));
		if (!empty($fields))
		{
			return $fields;
		}

		// by name
		$xpath  = ".//*[self::input | self::textarea | self::select][@name = $locator]";
		$fields = $this->browser->webDriver->findElements(WebDriverBy::xpath($xpath));
		if (!empty($fields))
		{
			return $fields;
		}

		// try to match by CSS or XPath
		$fields = $this->findElements($selector);
		if (!empty($fields))
		{
			return $fields;
		}

		throw new ElementNotFound($selector, "Field by name, label, CSS or XPath");
	}

	/**
	 * @param $selector
	 *
	 * @return WebDriverElement
	 * @throws \Codeception\Exception\ElementNotFound
	 */
	public function findField($selector)
	{
		$arr = $this->findFields($selector);

		return reset($arr);
	}

	public function fillField($selector, $value)
	{
		$this->findField($selector)->clear()->sendKeys($value);
	}

	/**
	 * @param $selector
	 *
	 * @return \Facebook\WebDriver\Remote\RemoteWebElement[]
	 */
	public function findElements($selector)
	{
		return $this->browser->webDriver->findElements($this->marshalSelector($selector));
	}

	/**
	 * @param $selector
	 *
	 * @return \Facebook\WebDriver\Remote\RemoteWebElement
	 */
	public function findElement($selector)
	{
		$arr = $this->findElements($selector);

		return reset($arr);
	}

	/**
	 * @param $selector
	 *
	 * @return mixed
	 */
	protected function marshalSelector($selector)
	{
		if (is_string($selector) && isset($this->elements[$selector]))
		{
			$selector = $this->elements[$selector];
		}

		return $selector;
	}
}
