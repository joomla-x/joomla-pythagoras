<?php

namespace Joomla\Tests\Unit\Content;

use Joomla\Content\Type\AbstractContentType;

class AbstractContentTypeTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @testdox The AbstractContentType supports a getter for a variable.
	 */
	public function testAbstractContentTypeMagicGetter()
	{
		/** @var AbstractContentType $mock */
		$mock = $this->getMockForAbstractClass(AbstractContentType::class, [], '', false);

		/** @noinspection PhpUndefinedFieldInspection */
		$mock->test = 'hello';

		$this->assertEquals('hello', $mock->__get('test'));
	}

	/**
	 * @testdox The AbstractContentType throws exception when no variable is
	 * set.
	 */
	public function testAbstractContentTypeMagicGetterInvalidVariable()
	{
		$this->setExpectedException(\UnexpectedValueException::class);

		/** @var AbstractContentType $mock */
		$mock = $this->getMockForAbstractClass(AbstractContentType::class, [], '', false);

		$mock->__get('test');
	}
}
