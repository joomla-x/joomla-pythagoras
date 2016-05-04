<?php
namespace Joomla\Tests\Functional\ORM\Storage\Doctrine;

use Doctrine\DBAL\Schema\Table;
use Joomla\ORM\Definition\Locator\Locator;
use Joomla\ORM\Definition\Locator\Strategy\RecursiveDirectoryStrategy;
use Joomla\ORM\Entity\Entity;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Storage\Doctrine\DoctrineCollectionFinder;
use Joomla\ORM\Storage\Doctrine\DoctrinePersistor;

class DoctrinePersistorTest extends BasicDoctrineTestCase
{

	public function testStoreNew()
	{
		$connection = $this->createConnection([
				[
						'id' => 1,
						'foo' => 'bar'
				]
		]);

		$persistor = new DoctrinePersistor($connection, [
				'table' => 'test'
		]);

		$builder = new EntityBuilder(new Locator([
				new RecursiveDirectoryStrategy(__DIR__)
		]));
		$entity = $builder->create('Test');
		$entity->bind([
				'foo' => 'bar1'
		]);
		$persistor->store($entity);

		$finder = new DoctrineCollectionFinder($connection, [
				'table' => 'test'
		], $builder);
		$finder->orderBy('id');

		$entities = $finder->getItems();

		$this->assertCount(2, $entities);
		$this->assertEquals('bar', $entities[0]->foo);
		$this->assertEquals('bar1', $entities[1]->foo);
	}

	public function testStoreUpdate()
	{
		$connection = $this->createConnection(
				[
						[
								'id' => 1,
								'foo' => 'bar'
						],
						[
								'id' => 2,
								'foo' => 'bar1'
						]
				]);

		$persistor = new DoctrinePersistor($connection, [
				'table' => 'test'
		]);

		$builder = new EntityBuilder(new Locator([
				new RecursiveDirectoryStrategy(__DIR__)
		]));
		$entity = $builder->create('Test');
		$entity->bind([
				'id' => 2,
				'foo' => 'bar2'
		]);
		$persistor->store($entity);

		$finder = new DoctrineCollectionFinder($connection, [
				'table' => 'test'
		], $builder);
		$finder->orderBy('id');

		$entities = $finder->getItems();

		$this->assertCount(2, $entities);
		$this->assertEquals('bar', $entities[0]->foo);
		$this->assertEquals('bar2', $entities[1]->foo);
	}

	public function testDelete()
	{
		$connection = $this->createConnection(
				[
						[
								'id' => 1,
								'foo' => 'bar'
						],
						[
								'id' => 2,
								'foo' => 'bar1'
						]
				]);

		$persistor = new DoctrinePersistor($connection, [
				'table' => 'test'
		]);

		$builder = new EntityBuilder(new Locator([
				new RecursiveDirectoryStrategy(__DIR__)
		]));
		$entity = $builder->create('Test');
		$entity->bind([
				'id' => 2,
				'foo' => 'bar2'
		]);
		$persistor->delete($entity);

		$finder = new DoctrineCollectionFinder($connection, [
				'table' => 'test'
		], $builder);
		$finder->orderBy('id');

		$entities = $finder->getItems();

		$this->assertCount(1, $entities);
		$this->assertEquals(1, $entities[0]->id);
	}

	public function testDeleteWrongId()
	{
		$connection = $this->createConnection(
				[
						[
								'id' => 1,
								'foo' => 'bar'
						],
						[
								'id' => 2,
								'foo' => 'bar1'
						]
				]);

		$persistor = new DoctrinePersistor($connection, [
				'table' => 'test'
		]);

		$builder = new EntityBuilder(new Locator([
				new RecursiveDirectoryStrategy(__DIR__)
		]));
		$entity = $builder->create('Test');
		$entity->bind([
				'id' => 3
		]);
		$persistor->delete($entity);

		$finder = new DoctrineCollectionFinder($connection, [
				'table' => 'test'
		], $builder);
		$finder->orderBy('id');

		$entities = $finder->getItems();

		$this->assertCount(2, $entities);
	}

	public function testDeleteEmptyId()
	{
		$connection = $this->createConnection(
				[
						[
								'id' => 1,
								'foo' => 'bar'
						],
						[
								'id' => 2,
								'foo' => 'bar1'
						]
				]);

		$persistor = new DoctrinePersistor($connection, [
				'table' => 'test'
		]);

		$builder = new EntityBuilder(new Locator([
				new RecursiveDirectoryStrategy(__DIR__)
		]));
		$entity = $builder->create('Test');
		$persistor->delete($entity);

		$finder = new DoctrineCollectionFinder($connection, [
				'table' => 'test'
		], $builder);
		$finder->orderBy('id');

		$entities = $finder->getItems();

		$this->assertCount(2, $entities);
	}
}
