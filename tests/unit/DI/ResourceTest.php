<?php
/**
 * @copyright  Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\DI;

use Joomla\DI\Container;
use Joomla\DI\Resource;

include_once 'Stubs/stubs.php';

/**
 * Tests for Resource class.
 */
class ResourceTest extends \PHPUnit_Framework_TestCase
{
	public function dataInstantiation()
	{
		return array(
			'shared, protected'         => array(
				'mode' => Resource::SHARE | Resource::PROTECT,
				'shared'    => true,
				'protected' => true
			),
			'shared, not protected (explicit)'     => array(
				'mode' => Resource::SHARE | Resource::NO_PROTECT,
				'shared'    => true,
				'protected' => false
			),
			'not shared, protected (explicit)'     => array(
				'mode' => Resource::NO_SHARE | Resource::PROTECT,
				'shared'    => false,
				'protected' => true
			),
			'not shared, not protected (explicit)' => array(
				'mode' => Resource::NO_SHARE | Resource::NO_PROTECT,
				'shared'    => false,
				'protected' => false
			),
			'shared, not protected (implicit)'     => array(
				'mode'      => Resource::SHARE,
				'shared'    => true,
				'protected' => false
			),
			'not shared, protected (implicit)'     => array(
				'mode'      => Resource::PROTECT,
				'shared'    => false,
				'protected' => true
			),
			'not shared, not protected (implicit)' => array(
				'mode'      => null,
				'shared'    => false,
				'protected' => false
			),
		);
	}

	/**
	 * @testdox The resource supports 'shared' and 'protected' modes, defaulting to 'not shared' and 'not protected'
	 * @dataProvider dataInstantiation
	 */
	public function testInstantiation($mode, $shared, $protected)
	{
		$container = new Container();

		if ($mode === null)
		{
			$descriptor = new Resource($container, 'dummy');
		}
		else
		{
			$descriptor = new Resource($container, 'dummy', $mode);
		}

		$this->assertEquals($shared, $descriptor->isShared());
		$this->assertEquals($protected, $descriptor->isProtected());
	}

	/**
	 * @testdox If a factory is provided, the instance is created on retrieval
	 */
	public function testGetInstanceWithFactory()
	{
		$container = new Container();
		$resource = new Resource(
			$container,
			function ()
			{
				return new Stub6();
			}
		);

		$this->assertInstanceOf('Joomla\\Tests\\Unit\\DI\\Stub6', $resource->getInstance());
	}

	/**
	 * @testdox If a factory is provided in non-shared mode, the instance is not cached
	 */
	public function testGetInstanceWithFactoryInNonSharedMode()
	{
		$container = new Container();
		$resource  = new Resource(
			$container,
			function ()
			{
				return new Stub6();
			},
			Resource::NO_SHARE
		);

		$one = $resource->getInstance();
		$two = $resource->getInstance();
		$this->assertNotSame($one, $two);
	}

	/**
	 * @testdox If a factory is provided in shared mode, the instance is cached
	 */
	public function testGetInstanceWithFactoryInSharedMode()
	{
		$container = new Container();
		$resource  = new Resource(
			$container,
			function ()
			{
				return new Stub6();
			},
			Resource::SHARE
		);

		$one = $resource->getInstance();
		$two = $resource->getInstance();
		$this->assertSame($one, $two);
	}

	/**
	 * @testdox If an instance is provided directly in shared mode, that instance is returned
	 */
	public function testGetInstanceWithInstanceInSharedMode()
	{
		$stub = new Stub6();
		$container = new Container();
		$resource  = new Resource(
			$container,
			$stub,
			Resource::SHARE
		);

		$this->assertSame($stub, $resource->getInstance());
	}

	/**
	 * @testdox If an instance is provided directly in non-shared mode, a copy (clone) of that instance is returned
	 */
	public function testGetInstanceWithInstanceInNonSharedMode()
	{
		$stub      = new Stub6();
		$container = new Container();
		$resource  = new Resource(
			$container,
			$stub,
			Resource::NO_SHARE
		);

		$this->assertNotSame($stub, $resource->getInstance());
	}

	/**
	 * @testdox After a reset, a new instance is returned even for shared resources
	 */
	public function testResetWithFactory()
	{
		$container = new Container();
		$resource  = new Resource(
			$container,
			function ()
			{
				return new Stub6();
			},
			Resource::SHARE
		);

		$one = $resource->getInstance();
		$resource->reset();
		$two = $resource->getInstance();
		$this->assertNotSame($one, $two);
	}

	/**
	 * @testdox After a reset, a new instance is returned even for shared resources
	 */
	public function testResetWithInstance()
	{
		$stub      = new Stub6();
		$container = new Container();
		$resource  = new Resource(
			$container,
			$stub,
			Resource::SHARE
		);

		$one = $resource->getInstance();
		$resource->reset();
		$two = $resource->getInstance();
		$this->assertNotSame($one, $two);
	}
}
