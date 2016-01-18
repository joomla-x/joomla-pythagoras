<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Exception\ModuleException;

class Acceptance extends \Codeception\Module
{
	public function getWebDriver()
	{
		foreach (['JoomlaBrowser', 'WebDriver', 'PhpBrowser'] as $name) {
			if ($this->hasModule($name)) {
				return parent::getModule($name)->webDriver;
			}
		}
		throw new ModuleException('Acceptance Hepler', 'No WebDriver compatible module found');
	}
}
