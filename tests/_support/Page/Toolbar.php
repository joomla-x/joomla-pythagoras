<?php
/**
 * Part of the Joomla CMS Test Environment
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Page;

class Toolbar
{
	/** @var  AbstractAdapter */
	protected $driver;

	/**
	 * Map menu paths to page classes
	 * Format of each entry is
	 * 'abstract menu path' => array(
	 *     'menu' => 'actual corresponding menu path',
	 *     'page' => 'Fully\\Qualified\\Class'
	 * )
	 *
	 * @var array
	 */
	protected $pageMap = array();

	protected $itemFormat = 'link text:%s';

	public function __construct(AbstractAdapter $driver)
	{
		$this->driver = $driver;
	}

	/**
	 * @param $menuItem
	 *
	 * @return bool
	 */
	public function itemExists($menuItem)
	{
		try
		{
			$this->item($menuItem);

			return true;
		}
		catch (\PHPUnit_Extensions_Selenium2TestCase_WebDriverException $e)
		{
			$this->debug($e->getMessage());
			$this->debug($e->getTraceAsString());

			return false;
		}
	}

	/**
	 * @param $button
	 *
	 * @return Element
	 */
	public function item($button)
	{
		$this->debug("Toolbar: " . $button);
		if (isset($this->pageMap[$button]))
		{
			$button = $this->pageMap[$button]['menu'];
			$this->debug(" => " . $button);
		}
		$this->debug("\n");

		$item = $this->driver->getElement(sprintf($this->itemFormat, urldecode($button)));

		return $item;
	}

	protected function debug($message)
	{
		$this->driver->debug($message);
	}

	/**
	 * @param $element
	 *
	 * @return Page
	 */
	public function select($element)
	{
		$pageClass = $this->getPageClass($element);

		$element = $this->item($element);
		$this->debug("Clicking element $element (" . $element->getId() . ")\n");
		$element->click();

		return $this->driver->pageFactoryCreate($pageClass);
	}

	/**
	 * @param   string $menuItem
	 *
	 * @return  string
	 */
	protected function getPageClass($menuItem)
	{
		if (!isset($this->pageMap[$menuItem]))
		{
			$menuItem = 'default';
		}
		if (isset($this->pageMap[$menuItem]))
		{
			$pageClass = $this->pageMap[$menuItem]['page'];
		}
		else
		{
			$pageClass = strtr($menuItem, array(' ' => '', '/' => '_'));
		}

		return $pageClass;
	}

	public function add($menuItem, $pageClass)
	{
		$this->pageMap[$menuItem] = array(
			'menu' => $menuItem,
			'page' => $pageClass
		);
	}

	public function remove($menuItem)
	{
		unset($this->pageMap[$menuItem]);
	}
}
