<?php
namespace Joomla\Tests\Unit\Service;

use Joomla\Service\CommandBusBuilder;
use Joomla\Tests\Unit\Service\Stubs\MockEventDispatcher;
use Joomla\Tests\Unit\Service\Stubs\SimpleCommand;
use Joomla\Tests\Unit\Service\Stubs\SimpleQuery;

class CommandBusTest extends \PHPUnit_Framework_TestCase
{
	private $commandBus;

	public function setUp()
	{
		$dispatcher = new MockEventDispatcher;
		$this->commandBus = (new CommandBusBuilder($dispatcher))->getCommandBus();
	}

	/**
	 * @testdox The test command bus has the CommandBus interface
	 */
	public function testTheTestCommandBusImplementsTheCommandBusInterface()
	{
		$this->assertInstanceOf('\\Joomla\\Service\\CommandBus', $this->commandBus);
	}

	/**
	 * @testdox The command bus has a handle method that takes a Command as a parameter
	 */
	public function testTheCommandBusHasAnExecuteMethodThatTakesACommandAsAParameter()
	{
		$this->assertTrue($this->commandBus->handle((new SimpleCommand)));
	}

	/**
	 * @testdox The command bus has a handle method that takes a Query as a parameter
	 */
	public function testTheCommandBusHasAnExecuteMethodThatTakesAQueryAsAParameter()
	{
		$this->assertEquals('XSome contentY', $this->commandBus->handle((new SimpleQuery('Some content'))));
	}
}
