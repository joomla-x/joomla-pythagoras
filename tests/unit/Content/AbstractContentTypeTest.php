<?php
namespace Joomla\Tests\Unit\Content;

use Joomla\Content\Type\AbstractContentType;

class AbstractContentTypeTest extends \PHPUnit_Framework_TestCase
{

	/**
	 * @testdox The AbstractContentType supports a getter for a variable.
	 */
	public function testAbstractContentTypeMagicGetter ()
	{
		$mock = $this->getMockForAbstractClass(AbstractContentType::class);

		$mock->test = 'hello';

		$this->assertEquals('hello', $mock->__get('test'));
	}

	/**
	 * @testdox The AbstractContentType throws exception when no variable is
	 * set.
	 */
	public function testAbstractContentTypeMagicGetterInvalidVariable ()
	{
		$this->setExpectedException(\UnexpectedValueException::class);

		$mock = $this->getMockForAbstractClass(AbstractContentType::class);
		$mock->__get('test');
	}
}
