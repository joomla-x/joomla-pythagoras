<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Exception\ModuleException;

class Acceptance extends \Codeception\Module
{
	/**
	 * @return \Codeception\Module\WebDriver
	 * @throws ModuleException
	 */
	public function getBrowser()
	{
		foreach (['JoomlaBrowser', 'WebDriver', 'PhpBrowser'] as $name) {
			if ($this->hasModule($name)) {
				return parent::getModule($name);
			}
		}
		throw new ModuleException('Acceptance Helper', 'No WebDriver compatible module found');
	}

	/**
	 * @return \Facebook\WebDriver\WebDriver
	 * @throws ModuleException
	 */
	public function getWebDriver()
	{
		return $this->getBrowser()->webDriver;
	}

	/**
	 * @param \Joomla\Tests\Page\Page $page
	 *
	 * @throws ModuleException
	 */
	public function assertCurrent(\Joomla\Tests\Page\Page $page)
	{
		$url = parse_url($this->getWebDriver()->getCurrentURL());
		$this->assertTrue($page->isCurrent(), 'Expected to be on ' . (string)$page . ', but actually on ' . $url['path']);
	}
}
