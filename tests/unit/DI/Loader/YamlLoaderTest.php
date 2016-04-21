<?php
/**
 * @copyright  Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Joomla\Tests\Unit\DI\Loader;
use Joomla\DI\Container;
use Joomla\DI\Loader\YamlLoader;
use Symfony\Component\Yaml\Exception\ParseException;
include_once __DIR__ . '/../Stubs/SimpleServiceProvider.php';

/**
 * Tests for YamlLoader class.
 */
class YamlLoaderTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @testdox Loading a string
	 */
	public function testLoadString ()
	{
		$content = <<<EOF
providers:
    foo:
        class: \SimpleServiceProvider
EOF;
		$container = new Container();

		$loader = new YamlLoader($container);
		$loader->load($content);

		$this->assertEquals('called', $container->get('foo'));
	}

	/**
	 * @testdox Loading a string with arguments
	 */
	public function testLoadStringWithArgument ()
	{
		$content = <<<EOF
providers:
    foo:
        class: \SimpleServiceProvider
        arguments: ['unit-test']
EOF;
		$container = new Container();
		$container->set('unit-test', 'called from case');

		$loader = new YamlLoader($container);
		$loader->load($content);

		$this->assertEquals('called from case', $container->get('foo'));
	}

	/**
	 * @testdox Loading an invalid string
	 */
	public function testLoadInvalidString ()
	{
		$this->setExpectedException(ParseException::class);

		$content = <<<EOF
providers unit test
    foo:
        class = \SimpleServiceProvider
EOF;
		$container = new Container();
		$container->set('unit-test', 'called from case');

		$loader = new YamlLoader($container);
		$loader->load($content);
	}

	/**
	 * @testdox Loading a file
	 */
	public function testLoadFile ()
	{
		$container = new Container();

		$loader = new YamlLoader($container);
		$loader->loadFromFile(dirname(__DIR__) . '/data/services.yml');

		$this->assertEquals('called', $container->get('foo'));
	}
}
