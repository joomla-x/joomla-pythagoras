<?php
namespace Joomla\Tests\Unit\Service;

use Joomla\Tests\Unit\Service\Stubs\ComplexQuery;
use Joomla\Tests\Unit\Service\Stubs\SimpleQuery;

class QueryTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @testdox The test query implements the Query interface
	 */
	public function testTheTestQueryImplementsTheQueryInterface()
	{
		$this->assertInstanceOf('\\Joomla\\Service\\Query', new SimpleQuery);
	}


	/**
	 * @testdox The test query is an Immutable object
	 */
	public function testTheTestQueryIsAnImmutableObject()
	{
		$this->assertInstanceOf('\\Joomla\\Service\\Immutable', new SimpleQuery);
	}


	/**
	 * @testdox The constructor argument can be retrieved by a getter method.
	 */
	public function testTheConstructorArgumentCanBeRetrievedByAGetterMethod()
	{
		$this->assertEquals('testing', (new SimpleQuery('testing'))->getTest());
	}


	/**
	 * @testdox The constructor argument can be retrieved as an object property.
	 */
	public function testTheConstructorArgumentCanBeRetrievedAsAnObjectProperty()
	{
		$this->assertEquals('testing', (new SimpleQuery('testing'))->test);
	}


	/**
	 * @testdox The getName method returns the name of the test query
	 */
	public function testTheGetnameMethodReturnsTheNameOfTheTestQuery()
	{
		$this->assertEquals('SimpleQuery', (new SimpleQuery('testing'))->getName());
	}


	/**
	 * @testdox The name property contains the name of the test query
	 */
	public function testTheNamePropertyContainsTheNameOfTheTestQuery()
	{
		$this->assertEquals('SimpleQuery', (new SimpleQuery('testing'))->name);
	}


	/**
	 * @testdox The getRequestedOn method does not throw an exception
	 */
	public function testTheGetrequestedonMethodDoesNotThrowAnException()
	{
		$this->assertNotEmpty((new SimpleQuery)->getRequestedOn());
	}


	/**
	 * @testdox Accessing the requestedOn property does not throw an exception
	 */
	public function testAccessingTheRequestedonPropertyDoesNotThrowAnException()
	{
		$this->assertNotEmpty((new SimpleQuery)->requestedOn);
	}


	/**
	 * @expectedException \RuntimeException
	 * @testdox Throws a \RuntimeException when trying to change the requestedOn time.
	 */
	public function testThrowsARuntimeexceptionWhenTryingToChangeTheRequestedonTime()
	{
		$query = new SimpleQuery;
		$query->requestedon = 'something';
	}


	/**
	 * @expectedException \RuntimeException
	 * @testdox Throws a \RuntimeException when trying to instantiate an invalid query
	 */
	public function testThrowsARuntimeexceptionWhenTryingToInstantiateAnInvalidQuery()
	{
		$invalidQuery = new ComplexQuery;
	}
}
