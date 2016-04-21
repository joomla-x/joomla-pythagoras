<?php
namespace Joomla\Tests\Unit\Service;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Plugin\PluginFactoryInterface;
use Joomla\Service\EventDispatcherServiceProvider;

class EventDispatcherServiceProviderTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @testdox The EventDispatcherServiceProvider implements the
	 * ServiceProviderInterface interface
	 */
	public function testTheTestEventDispatcherServiceProviderImplementsTheServiceProviderInterface ()
	{
		$this->assertInstanceOf(ServiceProviderInterface::class, new EventDispatcherServiceProvider());
	}

	/**
	 * @testdox The EventDispatcherServiceProvider adds an EventDispatcher to a
	 * container
	 */
	public function testEventDispatcherServiceProviderCreatesDispatcher ()
	{
		$container = new Container();
		$container->set('pluginfactory', $this->getMockBuilder(PluginFactoryInterface::class)
			->getMock());

		$service = new EventDispatcherServiceProvider();
		$service->register($container);

		$this->assertInstanceOf(DispatcherInterface::class, $container->get('EventDispatcher'));
	}
}
