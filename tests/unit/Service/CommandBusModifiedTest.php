<?php

namespace Joomla\Tests\Unit\Service;

use Joomla\Service\CommandBus;
use Joomla\Service\CommandBusBuilder;
use Joomla\Tests\Unit\Service\Stubs\Logger;
use Joomla\Tests\Unit\Service\Stubs\LoggingMiddleware;
use Joomla\Tests\Unit\Service\Stubs\MockEventDispatcher;
use Joomla\Tests\Unit\Service\Stubs\SimpleCommand;
use Joomla\Tests\Unit\Service\Stubs\SimpleQuery;

class CommandBusModifiedTest extends \PHPUnit_Framework_TestCase
{
    /** @var  CommandBus */
    private $commandBus;

    public function setUp()
    {
        $dispatcher        = new MockEventDispatcher;
        $commandBusBuilder = new CommandBusBuilder($dispatcher);

        $this->commandBus = $commandBusBuilder
            ->setMiddleware(
                array_merge(
                    [new LoggingMiddleware(new Logger)],
                    $commandBusBuilder->getMiddleware()
                )
            )
            ->getCommandBus()
        ;
    }

    /**
     * @testdox The modified command bus has the CommandBus interface
     */
    public function testTheTestCommandBusImplementsTheCommandBusInterface()
    {
        $this->assertInstanceOf('\\Joomla\\Service\\CommandBus', $this->commandBus);
    }

    /**
     * @testdox The modified command bus has an execute method that takes a Command as a parameter
     */
    public function testTheCommandBusHasAnExecuteMethodThatTakesACommandAsAParameter()
    {
        $this->expectOutputString(sprintf("LOG: Starting %1\$s\nLOG: Ending %1\$s\n",
            "Joomla\\Tests\\Unit\\Service\\Stubs\\SimpleCommand"));

        $this->assertTrue($this->commandBus->handle(new SimpleCommand));
    }

    /**
     * @testdox The modified command bus has an execute method that takes a Query as a parameter
     */
    public function testTheCommandBusHasAnExecuteMethodThatTakesAQueryAsAParameter()
    {
        $this->expectOutputString(sprintf("LOG: Starting %1\$s\nLOG: Ending %1\$s\n",
            "Joomla\\Tests\\Unit\\Service\\Stubs\\SimpleQuery"));

        $this->assertEquals('XSome contentY', $this->commandBus->handle((new SimpleQuery('Some content'))));
    }
}
