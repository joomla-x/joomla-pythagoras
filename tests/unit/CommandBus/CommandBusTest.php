<?php
namespace Joomla\Tests\Unit\CommandBus;
use Joomla\CommandBus\CommandBus;
use Joomla\Tests\Unit\CommandBus\Stubs\SimpleCommand;
use Joomla\Tests\Unit\CommandBus\Stubs\SimpleMiddleware;

class CommandBusTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @testdox The command bus has called the middleware
	 */
	public function testTheCommandBusHasCalledTheMiddleware ()
	{
		$middleWare = new SimpleMiddleware();
		$commandBus = new CommandBus([
				$middleWare
		]);
		$commandBus->handle(new SimpleCommand());
		
		$this->assertTrue($middleWare->called);
	}
}
