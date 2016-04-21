<?php
/**
 * @copyright  Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Joomla\Tests\Unit\DI\Loader;
use Joomla\DI\Container;
use Joomla\DI\Loader\IniLoader;
include_once __DIR__ . '/../Stubs/SimpleServiceProvider.php';

/**
 * Tests for the IniLoader class.
 */
class IniLoaderTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @testdox Loading a string
	 */
	public function testLoadString ()
	{
		$content = <<<EOF
[providers]
foo = "\\SimpleServiceProvider"
EOF;
		$container = new Container();

		$loader = new IniLoader($container);
		$loader->load($content);

		$this->assertEquals('called', $container->get('foo'));
	}

	/**
	 * @testdox Loading an invalid string
	 */
	public function testLoadInvalidString ()
	{
		$content = <<<EOF
[providers]
foo unit test
EOF;
		$container = new Container();

		$loader = new IniLoader($container);
		$loader->load($content);

		$this->assertFalse($container->has('foo'));
	}

	/**
	 * @testdox Loading an invalid class
	 */
	public function testLoadWithInvalidClass ()
	{
		$content = <<<EOF
[providers]
foo = "\\NotAvailableServiceProvider"
EOF;
		$container = new Container();

		$loader = new IniLoader($container);
		$loader->load($content);

		$this->assertFalse($container->has('foo'));
	}

	/**
	 * @testdox Loading a file
	 */
	public function testLoadFile ()
	{
		$container = new Container();

		$loader = new IniLoader($container);
		$loader->loadFromFile(dirname(__DIR__) . '/data/services.ini');

		$this->assertEquals('called', $container->get('foo'));
	}
}
