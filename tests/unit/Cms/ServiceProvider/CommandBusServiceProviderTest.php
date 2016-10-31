<?php

namespace Joomla\Tests\Unit\Cms\ServiceProvider;

use Joomla\Cms\ServiceProvider\CommandBusServiceProvider;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Service\CommandBus;
use Joomla\Tests\Unit\Service\Stubs\Logger;
use Joomla\Tests\Unit\Service\Stubs\LoggingMiddleware;
use Joomla\Tests\Unit\Service\Stubs\SimpleCommand;
use Joomla\Tests\Unit\Service\Stubs\SimpleQuery;

class CommandBusServiceProviderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @testdox The CommandBusServiceProvider implements the ServiceProviderInterface interface
	 */
	public function testTheTestCommandBusServiceProviderImplementsTheServiceProviderInterface()
	{
		$this->assertInstanceOf(ServiceProviderInterface::class, new CommandBusServiceProvider());
	}

	/**
	 * @testdox The CommandBusServiceProvider adds an CommandBus to a container
	 */
	public function testCommandBusServiceProviderCreatesCommandBus()
	{
		$container = new Container();
		$container->set('EventDispatcher', $this->getMockBuilder(DispatcherInterface::class)->getMock());

		$service = new CommandBusServiceProvider();
		$service->register($container);

		$this->assertInstanceOf(CommandBus::class, $container->get('CommandBus'));
	}

	/**
	 * @testdox The CommandBusServiceProvider adds an CommandBus to a container with an alias
	 */
	public function testCommandBusServiceProviderCreatesCommandBusWithAlias()
	{
		$container = new Container();
		$container->set('EventDispatcher', $this->getMockBuilder(DispatcherInterface::class)->getMock());

		$service = new CommandBusServiceProvider();
		$service->register($container, 'unit');

		$this->assertInstanceOf(CommandBus::class, $container->get('unit'));
	}

	/**
	 * @testdox The modified command bus has an execute method that takes a Command as a parameter
	 */
	public function testTheCommandBusHasAnExecuteMethodThatTakesACommandAsAParameter()
	{
		$this->expectOutputString(sprintf("LOG: Starting %1\$s\nLOG: Ending %1\$s\n", "Joomla\\Tests\\Unit\\Service\\Stubs\\SimpleCommand"));

		$container = new Container();
		$container->set('EventDispatcher', $this->getMockBuilder(DispatcherInterface::class)->getMock());
		$container->set('CommandBusMiddleware', [new LoggingMiddleware(new Logger())]);
		$container->registerServiceProvider(new CommandBusServiceProvider());

		$commandBus = $container->get('CommandBus');
		$this->assertTrue($commandBus->handle(new SimpleCommand()));
	}

	/**
	 * @testdox The modified command bus has an execute method that takes a Query as a parameter
	 */
	public function testTheCommandBusHasAnExecuteMethodThatTakesAQueryAsAParameter()
	{
		$this->expectOutputString(sprintf("LOG: Starting %1\$s\nLOG: Ending %1\$s\n", "Joomla\\Tests\\Unit\\Service\\Stubs\\SimpleQuery"));

		$container = new Container();
		$container->set('EventDispatcher', $this->getMockBuilder(DispatcherInterface::class)->getMock());
		$container->set('CommandBusMiddleware', [new LoggingMiddleware(new Logger())]);
		$container->registerServiceProvider(new CommandBusServiceProvider());

		$commandBus = $container->get('CommandBus');
		$this->assertEquals('XSome contentY', $commandBus->handle((new SimpleQuery('Some content'))));
	}
}
