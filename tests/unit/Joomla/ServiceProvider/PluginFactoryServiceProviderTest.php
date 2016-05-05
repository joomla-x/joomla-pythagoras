<?php

namespace Joomla\Tests\Unit\Joomla\ServiceProvider;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Plugin\PluginFactoryInterface;
use Joomla\Joomla\ServiceProvider\PluginFactoryServiceProvider;

class PluginFactoryServiceProviderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @testdox The PluginFactoryServiceProvider implements the
	 * ServiceProviderInterface interface
	 */
	public function testTheTestPluginFactoryServiceProviderImplementsTheServiceProviderInterface()
	{
		$this->assertInstanceOf(ServiceProviderInterface::class, new PluginFactoryServiceProvider());
	}

	/**
	 * @testdox The PluginFactoryServiceProvider adds an PluginFactory to a
	 * container
	 */
	public function testPluginFactoryServiceProviderCreatesPluginFactory()
	{
		$container = new Container();
		$container->set('ConfigDirectory', __DIR__);

		$service = new PluginFactoryServiceProvider();
		$service->register($container);

		$this->assertInstanceOf(PluginFactoryInterface::class, $container->get('PluginFactory'));
	}

	/**
	 * @testdox The PluginFactoryServiceProvider adds an PluginFactory to a
	 * container with an alias
	 */
	public function testPluginFactoryServiceProviderCreatesPluginFactoryWithAlias()
	{
		$container = new Container();
		$container->set('ConfigDirectory', __DIR__);

		$service = new PluginFactoryServiceProvider();
		$service->register($container, 'unit');

		$this->assertInstanceOf(PluginFactoryInterface::class, $container->get('unit'));
	}
}
