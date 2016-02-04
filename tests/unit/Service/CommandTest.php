<?php
namespace Joomla\Tests\Unit\Service;

use Joomla\Tests\Unit\Service\Stubs\ComplexCommand;
use Joomla\Tests\Unit\Service\Stubs\SimpleCommand;

class CommandTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @testdox The test command implements the Command interface
	 */
	public function testTheTestCommandImplementsTheCommandInterface()
	{
		$this->assertInstanceOf('\\Joomla\\Service\\Command', new SimpleCommand);
	}


	/**
	 * @testdox The test command is an Immutable object
	 */
	public function testTheTestCommandIsAnImmutableObject()
	{
		$this->assertInstanceOf('\\Joomla\\Service\\Immutable', new SimpleCommand);
	}


	/**
	 * @testdox The constructor argument can be retrieved by a getter method.
	 */
	public function testTheConstructorArgumentCanBeRetrievedByAGetterMethod()
	{
		$this->assertEquals('testing', (new SimpleCommand('testing'))->getTest());
	}


	/**
	 * @testdox The constructor argument can be retrieved as an object property.
	 */
	public function testTheConstructorArgumentCanBeRetrievedAsAnObjectProperty()
	{
		$this->assertEquals('testing', (new SimpleCommand('testing'))->test);
	}


	/**
	 * @testdox The getName method returns the name of the test command
	 */
	public function testTheGetnameMethodReturnsTheNameOfTheTestCommand()
	{
		$this->assertEquals('SimpleCommand', (new SimpleCommand('testing'))->getName());
	}


	/**
	 * @testdox The name property contains the name of the test command
	 */
	public function testTheNamePropertyContainsTheNameOfTheTestCommand()
	{
		$this->assertEquals('SimpleCommand', (new SimpleCommand('testing'))->name);
	}


	/**
	 * @testdox The getRequestedOn method does not throw an exception
	 */
	public function testTheGetrequestedonMethodDoesNotThrowAnException()
	{
		$this->assertNotEmpty((new SimpleCommand)->getRequestedOn());
	}


	/**
	 * @testdox Accessing the requestedOn property does not throw an exception
	 */
	public function testAccessingTheRequestedonPropertyDoesNotThrowAnException()
	{
		$this->assertNotEmpty((new SimpleCommand)->requestedOn);
	}


	/**
	 * @expectedException \RuntimeException
	 * @testdox Throws a RuntimeException when trying to change the requestedOn time.
	 */
	public function testThrowsARuntimeexceptionWhenTryingToChangeTheRequestedonTime()
	{
		$command = new SimpleCommand;
		$command->requestedon = 'something';
	}


	/**
	 * @expectedException \RuntimeException
	 * @testdox Throws a \RuntimeException when trying to instantiate an invalid command
	 */
	public function testThrowsARuntimeexceptionWhenTryingToInstantiateAnInvalidCommand()
	{
		$invalidCommand = new ComplexCommand;
	}
}
