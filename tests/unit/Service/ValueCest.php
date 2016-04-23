<?php
/**
 * Part of the Joomla Framework Service Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Service;

use Joomla\Tests\Unit\Service\Mock\Value;
use UnitTester;

class ValueCest
{
	public function _before(UnitTester $I)
	{
	}

	public function _after(UnitTester $I)
	{
	}

	/**
	 * @return array
	 */
	private function testData()
	{
		return [
			['string' => 'foo'],
			['integer' => 42],
			['array' => ['one' => 1, 'two' => 2, 'three' => 3]],
			['object' => (object)['one' => 1, 'two' => 2, 'three' => 3]],
			['value' => new Value(['key' => 'value'])],
		];
	}

	/**
	 * @return array
	 */
	private function modifiedData()
	{
		return [
			['string' => 'bar'],
			['integer' => 23],
			['array' => ['one' => 1, 'two' => 10, 'three' => 11]],
			['object' => (object)['one' => 1, 'two' => 10, 'three' => 11]],
			['value' => new Value(['key' => 'other'])],
		];
	}

	/**
	 * @testdox A value equals itself: a = a
	 */
	public function AValueEqualsItself(UnitTester $I)
	{
		foreach ($this->testData() as $key => $value)
		{
			$foo = new Value([$key => $value]);

			$I->assertTrue($foo->equals($foo));
		}
	}

	/**
	 * @testdox Equality is reflexive: a = b <=> b = a
	 */
	public function Reflexivity(UnitTester $I)
	{
		foreach ($this->testData() as $key => $value)
		{
			$foo = new Value([$key => $value]);
			$bar = new Value([$key => $value]);

			$I->assertTrue($foo->equals($bar));
			$I->assertTrue($bar->equals($foo));
		}
	}

	/**
	 * @testdox Differing values are recognised
	 */
	public function ValueInequality(UnitTester $I)
	{
		$testData = $this->testData();

		foreach ($this->modifiedData() as $key => $value)
		{
			$foo = new Value([$key => $testData[$key]]);
			$bar = new Value([$key => $value]);

			$I->assertFalse($foo->equals($bar));
			$I->assertFalse($bar->equals($foo));
		}
	}

	/**
	 * @testdox Differing type implies inequality
	 */
	public function TypeInequality(UnitTester $I)
	{
		$foo = new Value(['value' => 42]);
		$bar = new Value(['value' => '42']);

		$I->assertFalse($foo->equals($bar));
		$I->assertFalse($bar->equals($foo));
	}

	/**
	 * @testdox Differing class implies inequality
	 */
	public function ClassInequality(UnitTester $I)
	{
		$foo = new Value(['value' => new Value([])]);
		$bar = new Value(['value' => new \stdClass]);

		$I->assertFalse($foo->equals($bar));
		$I->assertFalse($bar->equals($foo));
	}
}
