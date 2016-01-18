<?php

namespace Joomla\Tests\Page;

use Facebook\WebDriver\WebDriver;

class Page
{
	/** @var PageFactory */
	protected $factory;

	/** @var WebDriver */
	protected $driver;

	/** @var  string */
	protected $url = '/';

	public function __construct(PageFactory $factory, WebDriver $driver)
	{
		$this->factory = $factory;
		$this->driver  = $driver;
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
		$url = parse_url($this->driver->getCurrentURL());

		return $url['path'] == $this->url;
	}
}
