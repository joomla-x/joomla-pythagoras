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
class ResourceDecoration extends \PHPUnit_Framework_TestCase
{
	/**
	 * @testdox An extended resource replaces the original resource
	 */
	public function testExtend()
	{
		$container = new Container();
		$container->share(
			'foo',
			function ()
			{
				return new \stdClass;
			}
		);

		$value = 42;

		$container->extend(
			'foo',
			function ($shared) use ($value)
			{
				$shared->value = $value;

				return $shared;
			}
		);

		$one = $container->get('foo');
		$this->assertEquals($value, $one->value);

		$two = $container->get('foo');
		$this->assertEquals($value, $two->value);

		$this->assertSame($one, $two);
	}

	/**
	 * @testdox Scalar resources can be extended
	 */
	public function testExtendScalar()
	{
		$container = new Container();

		$container->set('foo', 'bar');

		$this->assertEquals('bar', $container->get('foo'));

		$container->extend(
			'foo',
			function ($originalResult, Container $c)
			{
				return $originalResult . 'baz';
			}
		);

		$this->assertEquals('barbaz', $container->get('foo'));
	}

	/**
	 * @testdox Attempting to extend an undefined resource throws an InvalidArgumentException
	 * @expectedException  \InvalidArgumentException
	 */
	public function testExtendValidatesKeyIsPresent()
	{
		$container = new Container();
		$container->extend('foo', function () {});
	}

	/**
	 * @testdox A protected resource can not be extended
	 * @expectedException \Joomla\DI\Exception\ProtectedKeyException
	 */
	public function testExtendProtected()
	{
		$container = new Container();
		$container->protect(
			'foo',
			function ()
			{
				return new \stdClass;
			}
		);

		$value = 42;

		$container->extend(
			'foo',
			function ($shared) use ($value)
			{
				$shared->value = $value;

				return $shared;
			}
		);
	}
}
