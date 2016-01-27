<?php

namespace Joomla\Tests\Page;

use Codeception\Configuration;
use Facebook\WebDriver\WebDriver;

class Page
{
	/** @var PageFactory */
	protected $factory;

	/** @var WebDriver */
	protected $browser;

	/** @var  string */
	protected $url = '/';

	public function __construct(PageFactory $factory, WebDriver $browser)
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

	/**
	 * Determines, if this object represents the current page.
	 *
	 * @return boolean
	 */
	public function isCurrent()
	{
		$url = parse_url($this->browser->getCurrentURL());

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

		/** @var WebDriver $browser */
		$browser = $this->browser;
		$browser->takeScreenshot($outputDir . '/' . $pageName . '.png');
		file_put_contents($outputDir . '/' . $pageName . '.url', $browser->getCurrentURL());
		file_put_contents($outputDir . '/' . $pageName . '.html', $browser->getPageSource());
	}
}
