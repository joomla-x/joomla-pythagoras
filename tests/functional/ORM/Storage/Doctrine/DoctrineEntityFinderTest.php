<?php
namespace Joomla\Tests\Functional\ORM\Storage\Doctrine;

use Doctrine\DBAL\Schema\Table;
use Joomla\ORM\Definition\Locator\Locator;
use Joomla\ORM\Definition\Locator\Strategy\RecursiveDirectoryStrategy;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Finder\Operator;
use Joomla\ORM\Storage\Doctrine\DoctrineEntityFinder;

class DoctrineEntityFinderTest extends BasicDoctrineTestCase
{

	public function testGetEntity()
	{
		$connection = $this->createConnection([
				[
						'foo' => 'bar'
				],
				[
						'foo' => 'bar1'
				]
		]);

		$builder = new EntityBuilder(new Locator([
				new RecursiveDirectoryStrategy(__DIR__)
		]));
		$finder = new DoctrineEntityFinder($connection, [
				'table' => 'test'
		], $builder);
		$finder->with('foo', Operator::EQUAL, 'bar');

		$entity = $finder->get();

		$this->assertNotNull($entity);
		$this->assertEquals('bar', $entity->foo);
	}

	public function testGetNoEntity()
	{
		$connection = $this->createConnection([
				[
						'foo' => 'bar'
				]
		]);

		$builder = new EntityBuilder(new Locator([
				new RecursiveDirectoryStrategy(__DIR__)
		]));
		$finder = new DoctrineEntityFinder($connection, [
				'table' => 'test'
		], $builder);
		$finder->with('foo', Operator::EQUAL, 'bar1');

		$entity = $finder->get();

		$this->assertNull($entity);
	}
}
