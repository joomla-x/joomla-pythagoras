<?php
/**
 * Part of the Joomla Framework ORM Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\ORM\UnitOfWork;

use PHPUnit\Framework\TestCase;

class ObjectTest extends TestCase
{
	/**
	 * @testdox spl_object_hash() returns same value, even if object properties have changed
	 */
	public function testObjectIdentity()
	{
		$object     = new \stdClass;
		$hashBefore = spl_object_hash($object);

		$object->property = 'value';
		$hashAfter        = spl_object_hash($object);

		$this->assertEquals($hashBefore, $hashAfter);

		$reference     = $object;
		$hashReference = spl_object_hash($reference);

		$this->assertEquals($hashBefore, $hashReference);

		$clone     = clone($object);
		$hashClone = spl_object_hash($clone);

		$this->assertNotEquals($hashBefore, $hashClone);
	}

	/**
	 * @testdox md5(serialize()) returns different value, if object properties have changed
	 */
	public function testObjectChange()
	{
		$object     = new \stdClass;
		$hashBefore = md5(serialize($object));

		$object->property = 'value';
		$hashAfter        = md5(serialize($object));

		$this->assertNotEquals($hashBefore, $hashAfter);
	}
}
