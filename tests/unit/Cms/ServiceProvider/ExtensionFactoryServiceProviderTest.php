<?php

namespace Joomla\Tests\Unit\Cms\ServiceProvider;

use Joomla\Cms\ServiceProvider\ExtensionFactoryServiceProvider;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Extension\ExtensionFactoryInterface;

class ExtensionFactoryServiceProviderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @testdox The ExtensionFactoryServiceProvider implements the
	 * ServiceProviderInterface interface
	 */
	public function testTheTestExtensionFactoryServiceProviderImplementsTheServiceProviderInterface()
	{
		$this->assertInstanceOf(ServiceProviderInterface::class, new ExtensionFactoryServiceProvider());
	}

	/**
	 * @testdox The ExtensionFactoryServiceProvider adds an ExtensionFactory to a
	 * container
	 */
	public function testExtensionFactoryServiceProviderCreatesExtensionFactory()
	{
		$container = new Container();
		$container->set('ConfigDirectory', __DIR__);

		$service = new ExtensionFactoryServiceProvider();
		$service->register($container);

		$this->assertInstanceOf(ExtensionFactoryInterface::class, $container->get('ExtensionFactory'));
	}

	/**
	 * @testdox The ExtensionFactoryServiceProvider adds an ExtensionFactory to a
	 * container with an alias
	 */
	public function testExtensionFactoryServiceProviderCreatesExtensionFactoryWithAlias()
	{
		$container = new Container();
		$container->set('ConfigDirectory', __DIR__);

		$service = new ExtensionFactoryServiceProvider();
		$service->register($container, 'unit');

		$this->assertInstanceOf(ExtensionFactoryInterface::class, $container->get('unit'));
	}
}
