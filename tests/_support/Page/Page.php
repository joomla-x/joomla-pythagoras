<?php
/**
 * Part of the Joomla CMS Test Environment
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Page;

use Facebook\WebDriver\WebDriverElement;

abstract class Page
{
	/**
	 * @var \AcceptanceTester
	 */
	protected $tester;

	/** @var  string */
	protected $url = '/';

	public function __construct(\AcceptanceTester $I)
	{
		$this->tester = $I;
	}

	/**
	 * @return boolean
	 */
	abstract public function isCurrent();

	public function isPresent($locator, $timeout = null)
	{
		try
		{
			$this->getElement($locator, $timeout);

			return true;
		}
		catch (\Exception $e)
		{
			return false;
		}
	}

	/**
	 * @param string $locator The locator method:value
	 * @param int    $timeout
	 *
	 * @return WebDriverElement
	 */
	public function getElement($locator, $timeout = null)
	{
		$nodes = $this->tester->getScenario()->runStep(new \Codeception\Step\Action('_findElement', func_get_args()));

		return array_shift($nodes);
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->url;
	}
}
