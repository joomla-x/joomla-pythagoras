<?php
namespace Joomla\Tests\Unit\ORM\Storage\Doctrine;

use Doctrine\DBAL\Schema\Table;
use Joomla\ORM\Definition\Locator\Locator;
use Joomla\ORM\Definition\Locator\Strategy\RecursiveDirectoryStrategy;
use Joomla\ORM\Entity\Entity;
use Joomla\ORM\Entity\EntityBuilder;
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
	}
}
