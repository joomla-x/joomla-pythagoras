<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Exception\ModuleException;
use Facebook\WebDriver\WebDriver;

class Acceptance extends \Codeception\Module
{
	/**
	 * @return WebDriver
	 * @throws ModuleException
	 */
	public function getWebDriver()
	{
		foreach (['JoomlaBrowser', 'WebDriver', 'PhpBrowser'] as $name) {
			if ($this->hasModule($name)) {
				return parent::getModule($name)->webDriver;
			}
		}
		throw new ModuleException('Acceptance Hepler', 'No WebDriver compatible module found');
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
