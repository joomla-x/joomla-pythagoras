<?php
/**
 * @package        Joomla.FunctionalTest
 * @copyright      Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Tests\Page;

use AcceptanceTester;
use Facebook\WebDriver\WebDriver;

class PageFactory
{
	/** @var  AcceptanceTester */
	protected $tester;

	/** @var WebDriver */
	protected $driver;

	/** @var  string */
	protected $version;

	/** @var  string */
	protected $template;

	public function __construct(AcceptanceTester $I, $template, $version = 'Joomla3')
	{
		$this->tester = $I;
		$this->driver = $I->getWebDriver();
		$this->template = $template;
		$this->version = $version;
	}

	/**
	 * @param $pageClass
	 *
	 * @return Page
	 */
	public function create($pageClass)
	{
		$fqClassName = $this->getPrefix() . $pageClass;
		$page = $this->extendMenu(new $fqClassName($this, $this->driver));
		$this->debug("Created " . $fqClassName . "\n");

		return $page;
	}

	/**
	 * @param Page $pageObject
	 *
	 * @return Page
	 */
	public function extendMenu($pageObject)
	{
		if (isset($this->menuMapExtension) && !empty($pageObject->menu))
		{
			/** @var Menu $pageObject->menu */
			foreach ($this->menuMapExtension as $label => $page)
			{
				$pageObject->menu->add($label, $page);
			}
		}

		return $pageObject;
	}

	public function debug($text)
	{
		echo($text);
	}

	/**
	 * @return string
	 */
	private function getPrefix()
	{
		return '\\Joomla\\Tests\\Page\\' . $this->version . '\\' . $this->template . '\\';
	}
}
