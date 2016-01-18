<?php

namespace Joomla\Tests\Page;

use Codeception\Configuration;
use Facebook\WebDriver\WebDriver;

trait DumpTrait
{
	/**
	 * Dumps the current page.
	 *
	 * This trait expects the incorporating class to provide a WebDriver in the `$driver` property.
	 *
	 * Three files are generated:
	 *
	 *   - `<filename>.html`  The page's source code
	 *   - `<filename>.png`   A screenshot of the page
	 *   - `<filename>.url`   The actual URL of the page
	 *
	 * @param string $pageName The name of the page.
	 *                         It is recommended to supply the `__METHOD__` constant.
	 *                         However, any string can be used.
	 *                         Only characters valid for identifiers are allowed.
	 *                         '::' is replaced with '.', any other special character than '.' is removed.
	 *
	 * @throws \Codeception\Exception\ConfigurationException
	 */
	protected function dumpPage($pageName)
	{
		$pageName  = preg_replace(['~::~', '~[^\w\.]+~'], ['.', ''], $pageName);
		$outputDir = Configuration::outputDir();

		/** @var WebDriver $webDriver */
		$webDriver = $this->driver;
		$webDriver->takeScreenshot($outputDir . '/' . $pageName . '.png');
		file_put_contents($outputDir . '/' . $pageName . '.url', $webDriver->getCurrentURL());
		file_put_contents($outputDir . '/' . $pageName . '.html', $webDriver->getPageSource());
	}
}
