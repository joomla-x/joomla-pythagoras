<?php
namespace Joomla\Tests\Unit\Service;

use Joomla\Di\Container;
use Joomla\Service\CommandBusProvider;
use Joomla\Service\DispatcherProvider;
use Joomla\Service\QueryBusProvider;
use Joomla\Tests\Unit\Service\Stubs\SimpleCommand;
use Joomla\Tests\Unit\Service\Stubs\SimpleQuery;
use Joomla\Tests\Unit\Service\Stubs\SimpleService;

class ServiceTest extends \PHPUnit_Framework_TestCase
{
	private $container;

	public function setUp()
	{
		$this->container = (new Container)
			->registerServiceProvider(new CommandBusProvider)
			->registerServiceProvider(new QueryBusProvider)
			->registerServiceProvider(new DispatcherProvider)
		;
	}

	/**
	 * @testdox The test service is an instance of the Service class
	 */
	public function testTheTestServiceIsAnInstanceOfTheServiceClass()
	{
		$this->assertInstanceOf('\\Joomla\\Service\\Service', new SimpleService($this->container));
	}


	/**
	 * @testdox The service has an execute method that takes a Command as a parameter
	 */
	public function testTheServiceHasAnExecuteMethodThatTakesACommandAsAParameter()
	{
		$this->assertTrue((new SimpleService($this->container))->execute((new SimpleCommand)));
	}


	/**
	 * @testdox The service has an execute method that takes a Query as a parameter
	 */
	public function testTheServiceHasAnExecuteMethodThatTakesAQueryAsAParameter()
	{
		$this->assertEquals('XSome contentY', (new SimpleService($this->container))->execute((new SimpleQuery('Some content'))));
	}
}
