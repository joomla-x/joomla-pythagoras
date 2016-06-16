<?php
/**
 * Part of the Joomla CMS Test Environment
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Page;

class PageFactory
{
	/** @var \AcceptanceTester */
	private $tester;

	/** @var string */
	private $template;

	/** @var string */
	private $version;

	/**
	 * PageFactory constructor.
	 *
	 * @param \AcceptanceTester $I
	 * @param string            $template
	 * @param string            $version
	 */
	public function __construct(\AcceptanceTester $I, $template, $version = 'Joomla3')
	{
		$this->tester   = $I;
		$this->template = $template;
		$this->version  = $version;
	}

	/**
	 * @param string $pageClass
	 *
	 * @return Page
	 */
	public function create($pageClass)
	{
		$pageClass = 'Joomla\\Tests\\System\\Page\\' . $this->version . '\\' . $this->template . '\\' . $pageClass;

		return new $pageClass($this->tester);
	}
}
