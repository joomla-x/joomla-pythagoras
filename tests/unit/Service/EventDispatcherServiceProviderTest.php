<?php
namespace Joomla\Tests\Unit\Service;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\Dispatcher;
use Joomla\Service\EventDispatcherServiceProvider;
use Joomla\Event\DispatcherInterface;

class EventDispatcherServiceProviderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @testdox The EventDispatcherServiceProvider implements the ServiceProviderInterface interface
	 */
	public function testTheTestEventDispatcherServiceProviderImplementsTheServiceProviderInterface()
	{
		$this->assertInstanceOf(ServiceProviderInterface::class, new EventDispatcherServiceProvider());
	}

	/**
	 * @testdox The EventDispatcherServiceProvider adds an EventDispatcher to a container
	 */
	public function testEventDispatcherServiceProviderCreatesDispatcher()
	{
		$container = new Container();

		$service = new EventDispatcherServiceProvider();
		$service->register($container);

		$this->assertInstanceOf(DispatcherInterface::class, $container->get('EventDispatcher'));
	}
}
