<?php
/**
 * Part of the Joomla Framework ORM Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\ORM;

use Joomla\ORM\Entity\Entity;
use Joomla\ORM\Exception\EntityNotFoundException;
use Joomla\ORM\Finder\Operator;
use Joomla\ORM\Storage\CsvModel;
use Joomla\ORM\Storage\CsvProvider;
use UnitTester;

class CsvModelCest
{
	/** @var  CsvProvider */
	private $provider;

	public function _before(UnitTester $I)
	{
		$this->provider = new CsvProvider(__DIR__ . '/data/articles.csv');
	}

	public function _after(UnitTester $I)
	{
	}

	/**
	 * @return CsvModel
	 */
	private function entityModel()
	{
		return $this->provider->getEntityFinder('Article');
	}

	/**
	 * @return CsvModel
	 */
	private function collectionModel()
	{
		return $this->provider->getCollectionFinder('Article');
	}

	/**
	 * @testdox CsvModel (Entity) returns array on requested columns
	 */
	public function EntityModelReturnsArrayOnRequestedColumns(UnitTester $I)
	{
		$result = $this->entityModel()->columns(['*'])->with('id', Operator::EQUAL, 1)->get();

		$I->assertTrue(is_array($result));
	}

	/**
	 * @testdox CsvModel (Entity) returns only requested columns
	 */
	public function EntityModelReturnsRequestedColumns(UnitTester $I)
	{
		$result = $this->entityModel()->columns(['id', 'title'])->with('id', Operator::EQUAL, 1)->get();

		$I->assertEquals(['id' => 1, 'title' => 'First Article'], $result);
	}

	/**
	 * @testdox CsvModel (Entity) returns an Entity, if no columns are requested
	 */
	public function EntityModelReturnsEntityIfNoColumnsSpecified(UnitTester $I)
	{
		$result = $this->entityModel()->with('id', Operator::EQUAL, 1)->get();

		$I->assertTrue($result instanceof Entity);
	}

	/**
	 * @testdox Columns can be specified as comma separated string
	 */
	public function ColumnsSpecifiedAsCommaSeparatedString(UnitTester $I)
	{
		$result = $this->entityModel()->columns('id, title')->with('id', Operator::EQUAL, 1)->get();

		$I->assertEquals(['id' => 1, 'title' => 'First Article'], $result);
	}

	/**
	 * @testdox CsvModel (Entity) throws EntityNotFoundException, if no result is found
	 */
	public function EntityModelThrowsExceptionIfNoResult(UnitTester $I)
	{
		try
		{
			$result = $this->entityModel()->with('id', Operator::EQUAL, 0)->get();
			$I->fail('Expected EntityNotFoundException not thrown');
		} catch (\Exception $e)
		{
			$I->assertTrue($e instanceof EntityNotFoundException);
		}
	}

	/**
	 * @testdox CsvModel (Collection) returns arrays on requested columns
	 */
	public function CollectionModelReturnsArrayOnRequestedColumns(UnitTester $I)
	{
		$result = $this->collectionModel()->columns(['*'])->with('id', Operator::EQUAL, 1)->get();

		$I->assertEquals(1, count($result));
		$I->assertTrue(is_array($result[0]));
	}

	/**
	 * @testdox CsvModel (Collection) returns only requested columns
	 */
	public function CollectionModelReturnsRequestedColumns(UnitTester $I)
	{
		$result = $this->collectionModel()->columns(['id', 'title'])->with('id', Operator::EQUAL, 1)->get();

		$I->assertEquals([['id' => 1, 'title' => 'First Article']], $result);
	}

	/**
	 * @testdox CsvModel (Collection) returns an Collection, if no columns are requested
	 */
	public function CollectionModelReturnsCollectionIfNoColumnsSpecified(UnitTester $I)
	{
		$result = $this->collectionModel()->with('id', Operator::EQUAL, 1)->get();

		$I->assertTrue($result[0] instanceof Entity);
	}

	/**
	 * @testdox CsvModel (Collection) returns empty array, if no result is found
	 */
	public function CollectionModelReturnsEmptyArrayIfNoResult(UnitTester $I)
	{
		$result = $this->collectionModel()->with('id', Operator::EQUAL, 0)->get();
		$I->assertEquals([], $result);
	}

	/**
	 * @testdox Result set can be ordered
	 */
	public function ResultSetCanBeOrdered(UnitTester $I)
	{
		$result = $this->collectionModel()->columns(['id', 'title'])->get();
		$I->assertEquals(
			[
				['id' => 1, 'title' => 'First Article'],
				['id' => 2, 'title' => 'Second Article'],
				['id' => 3, 'title' => 'Part One'],
				['id' => 4, 'title' => 'Part Two'],
			],
			$result
		);

		$result = $this->collectionModel()->columns(['id', 'title'])->orderBy('id', 'DESC')->get();
		$I->assertEquals(
			[
				['id' => 4, 'title' => 'Part Two'],
				['id' => 3, 'title' => 'Part One'],
				['id' => 2, 'title' => 'Second Article'],
				['id' => 1, 'title' => 'First Article'],
			],
			$result
		);

		$result = $this->collectionModel()->columns(['id', 'title'])->orderBy('title')->get();
		$I->assertEquals(
			[
				['id' => 1, 'title' => 'First Article'],
				['id' => 3, 'title' => 'Part One'],
				['id' => 4, 'title' => 'Part Two'],
				['id' => 2, 'title' => 'Second Article'],
			],
			$result
		);
	}

	/**
	 * @testdox Result set can be sliced without explicit start
	 */
	public function ResultSetCanBeSlicedWithoutStart(UnitTester $I)
	{
		$result = $this->collectionModel()->columns(['id', 'title'])->get(2);
		$I->assertEquals(
			[
				['id' => 1, 'title' => 'First Article'],
				['id' => 2, 'title' => 'Second Article'],
			],
			$result
		);
	}

	/**
	 * @testdox Result set can be sliced
	 */
	public function ResultSetCanBeSliced(UnitTester $I)
	{
		$result = $this->collectionModel()->columns(['id', 'title'])->get(2, 1);
		$I->assertEquals(
			[
				['id' => 2, 'title' => 'Second Article'],
				['id' => 3, 'title' => 'Part One'],
			],
			$result
		);
	}

	public function SupportsEqualOperator(UnitTester $I)
	{
		$result = $this->collectionModel()->columns(['id', 'title'])->with('id', Operator::EQUAL, 1)->get();
		$I->assertEquals(
			[
				['id' => 1, 'title' => 'First Article'],
			],
			$result
		);
	}

	public function SupportsNotEqualOperator(UnitTester $I)
	{
		$result = $this->collectionModel()->columns(['id', 'title'])->with('id', Operator::NOT_EQUAL, 1)->get();
		$I->assertEquals(
			[
				['id' => 2, 'title' => 'Second Article'],
				['id' => 3, 'title' => 'Part One'],
				['id' => 4, 'title' => 'Part Two'],
			],
			$result
		);
	}

	public function SupportsGreaterThanOperator(UnitTester $I)
	{
		$result = $this->collectionModel()->columns(['id', 'title'])->with('id', Operator::GREATER_THAN, 2)->get();
		$I->assertEquals(
			[
				['id' => 3, 'title' => 'Part One'],
				['id' => 4, 'title' => 'Part Two'],
			],
			$result
		);
	}

	public function SupportsGreaterThanOrEqualOperator(UnitTester $I)
	{
		$result = $this->collectionModel()->columns(['id', 'title'])->with('id', Operator::GREATER_OR_EQUAL, 2)->get();
		$I->assertEquals(
			[
				['id' => 2, 'title' => 'Second Article'],
				['id' => 3, 'title' => 'Part One'],
				['id' => 4, 'title' => 'Part Two'],
			],
			$result
		);
	}

	public function SupportsLessThanOperator(UnitTester $I)
	{
		$result = $this->collectionModel()->columns(['id', 'title'])->with('id', Operator::LESS_THAN, 3)->get();
		$I->assertEquals(
			[
				['id' => 1, 'title' => 'First Article'],
				['id' => 2, 'title' => 'Second Article'],
			],
			$result
		);
	}

	public function SupportsLessThanOrEqualOperator(UnitTester $I)
	{
		$result = $this->collectionModel()->columns(['id', 'title'])->with('id', Operator::LESS_OR_EQUAL, 3)->get();
		$I->assertEquals(
			[
				['id' => 1, 'title' => 'First Article'],
				['id' => 2, 'title' => 'Second Article'],
				['id' => 3, 'title' => 'Part One'],
			],
			$result
		);
	}

	public function SupportsContainsOperator(UnitTester $I)
	{
		$result = $this->collectionModel()->columns(['id', 'title'])->with('title', Operator::CONTAINS, 'Article')
					   ->get();
		$I->assertEquals(
			[
				['id' => 1, 'title' => 'First Article'],
				['id' => 2, 'title' => 'Second Article'],
			],
			$result
		);
	}

	public function SupportsStartsWithOperator(UnitTester $I)
	{
		$result = $this->collectionModel()->columns(['id', 'title'])->with('title', Operator::STARTS_WITH, 'Part')
					   ->get();
		$I->assertEquals(
			[
				['id' => 3, 'title' => 'Part One'],
				['id' => 4, 'title' => 'Part Two'],
			],
			$result
		);
	}

	public function SupportsEndsWithOperator(UnitTester $I)
	{
		$result = $this->collectionModel()->columns(['id', 'title'])->with('title', Operator::ENDS_WITH, 'Article')
					   ->get();
		$I->assertEquals(
			[
				['id' => 1, 'title' => 'First Article'],
				['id' => 2, 'title' => 'Second Article'],
			],
			$result
		);
	}

	public function SupportsMatchesOperator(UnitTester $I)
	{
		$result = $this->collectionModel()->columns(['id', 'title'])->with('title', Operator::MATCHES, 'rt\\s')->get();
		$I->assertEquals(
			[
				['id' => 3, 'title' => 'Part One'],
				['id' => 4, 'title' => 'Part Two'],
			],
			$result
		);
	}

	public function SupportsInOperator(UnitTester $I)
	{
		$result = $this->collectionModel()->columns(['id', 'title'])->with('id', Operator::IN, '1,3')->get();
		$I->assertEquals(
			[
				['id' => 1, 'title' => 'First Article'],
				['id' => 3, 'title' => 'Part One'],
			],
			$result
		);
	}
}
