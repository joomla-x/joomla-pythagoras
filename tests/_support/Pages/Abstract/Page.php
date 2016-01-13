<?php
namespace Celtic\Testing\Joomla;

abstract class Page
{
	/** @var  AbstractAdapter */
	protected $driver;

	/** @var  Menu */
	public $menu;

	public function __construct(AbstractAdapter $driver)
	{
		$this->driver = $driver;
	}

	protected function debug($message)
	{
		$this->driver->debug($message);
	}

	/**
	 * Check whether the current page matches this class
	 *
	 * @return  bool
	 */
	abstract public function isCurrent();

	public function isPresent($locator, $timeout = null)
	{
		try {
			$this->getElement($locator, $timeout);
			return true;
		}
		catch (\PHPUnit_Extensions_Selenium2TestCase_WebDriverException $e)
		{
			return false;
		}
	}

	/**
	 * @param string $locator The locator method:value
	 * @param int $timeout Max time to wait (in milliseconds)
	 *
	 * @return \PHPUnit_Extensions_Selenium2TestCase_Element
	 */
	public function getElement($locator, $timeout = null)
	{
		return $this->driver->getElement($locator, $timeout);
	}
}
