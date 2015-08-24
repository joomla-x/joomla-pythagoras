<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Registry
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Test class for JRegistryFormat.
 * Generated by PHPUnit on 2009-10-27 at 15:08:23.
 *
 * @package     Joomla.UnitTest
 * @subpackage  Registry
 * @since       11.1
 */
class JRegistryFormatTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Test the JRegistryFormat::getInstance method.
	 *
	 * @return void
	 */
	public function testGetInstance()
	{
		// Test INI format.
		$object = JRegistryFormat::getInstance('INI');
		$this->assertInstanceOf(
			'JRegistryFormatIni',
			$object
		);

		// Test JSON format.
		$object = JRegistryFormat::getInstance('JSON');
		$this->assertInstanceOf(
			'JRegistryFormatJson',
			$object
		);

		// Test PHP format.
		$object = JRegistryFormat::getInstance('PHP');
		$this->assertInstanceOf(
			'JRegistryFormatPhp',
			$object
		);

		// Test XML format.
		$object = JRegistryFormat::getInstance('XML');
		$this->assertInstanceOf(
			'JRegistryFormatXml',
			$object
		);

		// Test non-existing format.
		try
		{
			$object = JRegistryFormat::getInstance('SQL');
		}
		catch (Exception $e)
		{
			return;
		}
		$this->fail('JRegistryFormat should throw an exception in case of non-existing formats');
	}
}
