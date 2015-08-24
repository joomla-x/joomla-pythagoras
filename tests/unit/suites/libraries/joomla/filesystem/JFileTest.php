<?php
/**
 * @package    Joomla.UnitTest
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JFile.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Event
 * @since       11.1
 */
class JFileTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Test...
	 *
	 * @return void
	 */
	public function testExists()
	{
		$this->assertTrue(
			JFile::exists(__FILE__)
		);
	}
}
