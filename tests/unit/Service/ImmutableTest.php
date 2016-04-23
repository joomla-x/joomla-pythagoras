<?php
namespace Joomla\Tests\Unit\Service;

use Joomla\Tests\Unit\Service\Stubs\ImmutableClass;

class ImmutableTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @expectedException \InvalidArgumentException
	 * @testdox Throws a \InvalidArgumentException when trying to get a non-existant property
	 */
	public function testThrowsAnInvalidArgumentExceptionWhenTryingToGetANonexistantProperty()
	{
		$something = (new ImmutableClass)->doesNotExist;
	}


	/**
	 * @expectedException \InvalidArgumentException
	 * @testdox Throws a \InvalidArgumentException when trying to create a new property
	 */
	public function testThrowsAnInvalidArgumentExceptionWhenTryingToCreateANewProperty()
	{
		$testObject = new ImmutableClass;
		$testObject->test = 'something';
	}


	/**
	 * @testdox The constructor argument can be retrieved by a getter method.
	 */
	public function testTheConstructorArgumentCanBeRetrievedByAGetterMethod()
	{
		$this->assertEquals('testing', (new ImmutableClass('testing'))->getTest());
	}


	/**
	 * @testdox The constructor argument can be retrieved as an object property.
	 */
	public function testTheConstructorArgumentCanBeRetrievedAsAnObjectProperty()
	{
		$this->assertEquals('testing', (new ImmutableClass('testing'))->test);
	}


	/**
	 * @testdox Property names are not case-sensitive.
	 */
	public function testPropertyNamesAreNotCasesensitive()
	{
		$this->assertEquals('testing', (new ImmutableClass('testing'))->TeSt);
	}
}
