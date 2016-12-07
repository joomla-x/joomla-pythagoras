<?php

namespace Joomla\Tests\Unit\Cms\ServiceProvider;

use Joomla\Cms\ServiceProvider\ConfigServiceProvider;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Registry\Registry;

class ConfigServiceProviderTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @testdox The ConfigServiceProvider implements the ServiceProviderInterface interface
	 */
	public function testTheTestConfigServiceProviderImplementsTheServiceProviderInterface()
	{
		$this->assertInstanceOf(ServiceProviderInterface::class, new ConfigServiceProvider());
	}

	/**
	 * @testdox The ConfigServiceProvider adds an config to a container
	 */
	public function testConfigServiceProviderCreatesConfig()
	{
		$container = new Container();
		$container->set('ConfigDirectory', __DIR__ . '/data');
		$container->set('ConfigFileName', 'env.txt');

		$service = new ConfigServiceProvider();
		$service->register($container);

		$this->assertInstanceOf(Registry::class, $container->get('config'));
	}

	/**
	 * @testdox The ConfigServiceProvider adds an config to a container with an alias
	 */
	public function testConfigServiceProviderCreatesConfigWithAlias()
	{
		$container = new Container();
		$container->set('ConfigDirectory', __DIR__ . '/data');
		$container->set('ConfigFileName', 'env.txt');

		$service = new ConfigServiceProvider();
		$service->register($container, 'unit');

		$this->assertInstanceOf(Registry::class, $container->get('unit'));
	}

	/**
	 * @testdox The ConfigServiceProvider adds an config to a container with variables from the environment
	 */
	public function testConfigServiceProviderCreatesConfigFromEnv()
	{
		$container = new Container();
		$container->set('ConfigDirectory', __DIR__ . '/data');
		$container->set('ConfigFileName', 'env.txt');

		$service = new ConfigServiceProvider();
		$service->register($container);

		/** @var Registry $config * */
		$config = $container->get('config');

		$this->assertEquals('bar', $config->get('foo'));
	}
}
