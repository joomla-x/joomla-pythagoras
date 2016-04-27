<?php

namespace Joomla\Tests\Unit\Service;

use Joomla\CommandBus\CommandBus;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Service\CommandBusServiceProvider;
use Joomla\Tests\Unit\Service\Stubs\LoggingMiddleware;
use Joomla\Tests\Unit\Service\Stubs\Logger;
use Joomla\Tests\Unit\CommandBus\Stubs\SimpleCommand;
use Joomla\Tests\Unit\CommandBus\Stubs\SimpleQuery;

class CommandBusServiceProviderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @testdox The CommandBusServiceProvider implements the
	 * ServiceProviderInterface interface
	 */
	public function testTheTestCommandBusServiceProviderImplementsTheServiceProviderInterface()
	{
		$this->assertInstanceOf(ServiceProviderInterface::class, new CommandBusServiceProvider());
	}

	/**
	 * @testdox The CommandBusServiceProvider adds an CommandBus to a
	 * container
	 */
	public function testCommandBusServiceProviderCreatesDispatcher()
	{
		$container = new Container();

		$service = new CommandBusServiceProvider();
		$service->register($container);

		$this->assertInstanceOf(CommandBus::class, $container->get('CommandBus'));
	}
	
	/**
	 * @testdox The modified command bus has an execute method that takes a Command as a parameter
	 */
	public function testTheCommandBusHasAnExecuteMethodThatTakesACommandAsAParameter()
	{
		$this->expectOutputString(sprintf("LOG: Starting %1\$s\nLOG: Ending %1\$s\n", "Joomla\\Tests\\Unit\\CommandBus\\Stubs\\SimpleCommand"));
	
		$container = new Container();
		$container->set('CommandBusMiddleware', [new LoggingMiddleware(new Logger)]);
		$container->registerServiceProvider(new CommandBusServiceProvider());
		
		$commandBus = $container->get('CommandBus');
		$this->assertTrue(is_array($commandBus->handle(new SimpleCommand())));
	}
	
	/**
	 * @testdox The modified command bus has an execute method that takes a Query as a parameter
	 */
	public function testTheCommandBusHasAnExecuteMethodThatTakesAQueryAsAParameter()
	{
		$this->expectOutputString(sprintf("LOG: Starting %1\$s\nLOG: Ending %1\$s\n", "Joomla\\Tests\\Unit\\CommandBus\\Stubs\\SimpleQuery"));
	
		$container = new Container();
		$container->set('CommandBusMiddleware', [new LoggingMiddleware(new Logger())]);
		$container->registerServiceProvider(new CommandBusServiceProvider());
		
		$commandBus = $container->get('CommandBus');
		$this->assertEquals('XSome contentY', $commandBus->handle((new SimpleQuery('Some content'))));
	}
}
