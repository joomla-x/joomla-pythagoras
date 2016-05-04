<?php
namespace Joomla\Tests\Functional\ORM\Storage\Doctrine;

use Doctrine\DBAL\Schema\Table;
use Joomla\ORM\Definition\Locator\Locator;
use Joomla\ORM\Definition\Locator\Strategy\RecursiveDirectoryStrategy;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Finder\Operator;
use Joomla\ORM\Storage\Doctrine\DoctrineCollectionFinder;

class DoctrineCollectionFinderTest extends BasicDoctrineTestCase
{

	public function testGetEntities()
	{
		$connection = $this->createConnection(
				[
						[
								'foo' => 1
						],
						[
								'foo' => 2
						],
						[
								'foo' => 3
						]
				]);

		$builder = new EntityBuilder(new Locator([
				new RecursiveDirectoryStrategy(__DIR__)
		]));
		$finder = new DoctrineCollectionFinder($connection, [
				'table' => 'test'
		], $builder);
		$finder->columns([
				'foo'
		]);
		$finder->orderBy('foo');
		$finder->with('foo', Operator::GREATER_THAN, 1);

		$entities = $finder->getItems();

		$this->assertCount(2, $entities);
		$this->assertEquals(2, $entities[0]->foo);
		$this->assertEquals(3, $entities[1]->foo);
	}

	public function testGetEntitiesWrongOperator()
	{
		$connection = $this->createConnection([
				[
						'foo' => 1
				],
				[
						'foo' => 2
				]
		]);

		$builder = new EntityBuilder(new Locator([
				new RecursiveDirectoryStrategy(__DIR__)
		]));
		$finder = new DoctrineCollectionFinder($connection, [
				'table' => 'test'
		], $builder);
		$finder->with('foo', 'wrong', 1);
		$finder->orderBy('foo');

		$entities = $finder->getItems();

		$this->assertCount(2, $entities);
		$this->assertEquals(1, $entities[0]->foo);
		$this->assertEquals(2, $entities[1]->foo);
	}

	public function testEmptyGet()
	{
		$connection = $this->createConnection([
				[
						'foo' => 1
				]
		]);

		$builder = new EntityBuilder(new Locator([
				new RecursiveDirectoryStrategy(__DIR__)
		]));
		$finder = new DoctrineCollectionFinder($connection, [
				'table' => 'test'
		], $builder);
		$finder->with('foo', Operator::EQ, 2);

		$entities = $finder->getItems();

		$this->assertEmpty($entities);
	}

	public function testGetEntitiesWithDifferentEntityName()
	{
		$connection = $this->createConnection([
				[
						'foo' => 1
				],
				[
						'foo' => 2
				]
		], 'test1');

		$builder = new EntityBuilder(new Locator([
				new RecursiveDirectoryStrategy(__DIR__)
		]));
		$finder = new DoctrineCollectionFinder($connection, [
				'table' => 'test1',
				'entity_name' => 'Test'
		], $builder);
		$finder->orderBy('foo');

		$entities = $finder->getItems();

		$this->assertCount(2, $entities);
		$this->assertEquals(1, $entities[0]->foo);
		$this->assertEquals(2, $entities[1]->foo);
	}
}
