<?php
/**
 * @package	    Joomla.UnitTest
 * @subpackage  Editor
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license	    GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\CMS\Editor\Editor;

/**
 * Test class for JEditor.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Editor
 * @since       3.0
 */
class JEditorTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Object under test
	 *
	 * @var    Editor
	 * @since  3.0
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	protected function setUp()
	{
		$this->object = new Editor;
	}

	/**
	 * Tests the getInstance method
	 *
	 * @return  void
	 *
	 * @since   3.0
	 * @covers  Editor::getInstance
	 */
	public function testGetInstance()
	{
		$this->assertInstanceOf(
			'\\Joomla\\CMS\\Editor\\Editor',
			Editor::getInstance('none')
		);
	}
}
