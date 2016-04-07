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
	 * @testdox The test dispatcher ervice provider implements the ServiceProviderInterface interface
	 */
	public function testTheTestEventDispatcherServiceProviderImplementsTheCommandInterface()
	{
		$this->assertInstanceOf(ServiceProviderInterface::class, new EventDispatcherServiceProvider());
	}

	/**
	 * @testdox The test EventDispatcherServiceProvider has registered EventDispatcher service
	 */
	public function testContainerHasDispatcherServiceRegistered()
	{
		$container = $this->getMockBuilder(Container::class)->getMock();
		$container->expects($this->once())->method('set')->with(
			$this->equalTo('EventDispatcher'),
			$this->callback(function ($callable)
				{
					return is_callable($callable);
				}
			),
			$this->equalTo(true),
			$this->equalTo(true)
		);

		$service = new EventDispatcherServiceProvider();
		$service->register($container);
	}

	/**
	 * @testdox The test EventDispatcherServiceProvider creates Dispatcher
	 */
	public function testEventDispatcherServiceProviderCreatesDispatcher()
	{
		$container = new Container();

		$service = new EventDispatcherServiceProvider();
		$service->register($container);

		$this->assertInstanceOf(DispatcherInterface::class, $container->get('EventDispatcher'));
	}
}
