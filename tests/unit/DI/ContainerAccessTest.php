<?php
/**
 * @copyright  Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\DI;

use Joomla\DI\Container;

include_once 'Stubs/stubs.php';

/**
 * Tests for Container class.
 */
class ContainerAccessTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @testdox The same resource instance is returned for shared resources
	 */
	public function testGetShared()
	{
		$container = new Container();
		$container->set(
			'foo',
			function ()
			{
				return new \stdClass;
			},
			true
		);

		$this->assertSame($container->get('foo'), $container->get('foo'));
	}

	/**
	 * @testdox A new resource instance is returned for non-shared resources
	 */
	public function testGetNotShared()
	{
		$container = new Container();
		$container->set(
			'foo',
			function ()
			{
				return new \stdClass;
			},
			false
		);

		$this->assertNotSame($container->get('foo'), $container->get('foo'));
	}

	/**
	 * @testdox Accessing an undefined resource throws an InvalidArgumentException
	 * @expectedException  \InvalidArgumentException
	 */
	public function testGetNotExists()
	{
		$container = new Container();
		$container->get('foo');
	}

	/**
	 * @testdox The existence of a resource can be checked
	 */
	public function testExists()
	{
		$container = new Container();
		$container->set('foo', 'bar');

		$this->assertTrue($container->has('foo'), "'foo' should be present");
		$this->assertFalse($container->has('baz'), "'baz' should not be present");
	}

	/**
	 * @testdox getNewInstance() will always return a new instance, even if the resource was set to be shared
	 */
	public function testGetNewInstance()
	{
		$container = new Container();
		$container->share(
			'foo',
			function ()
			{
				return new \stdClass;
			}
		);

		$this->assertNotSame($container->getNewInstance('foo'), $container->getNewInstance('foo'));
	}
}
