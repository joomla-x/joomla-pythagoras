<?php
namespace Joomla\Tests\Unit\ORM\Storage\Doctrine;

use Joomla\ORM\Definition\Locator\Locator;
use Joomla\ORM\Definition\Locator\Strategy\RecursiveDirectoryStrategy;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Exception\EntityNotFoundException;
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
		$finder = new DoctrineEntityFinder($connection, 'test', 'Test', $builder);
		$finder->with('foo', Operator::EQUAL, 'bar');

		$entity = $finder->getItem();

		$this->assertNotNull($entity);
		$this->assertEquals('bar', $entity->foo);
	}

	public function testGetNoEntity()
	{
		$this->setExpectedException(EntityNotFoundException::class);

		$connection = $this->createConnection([]);

		$builder = new EntityBuilder(new Locator([
				new RecursiveDirectoryStrategy(__DIR__)
		]));
		$finder = new DoctrineEntityFinder($connection, 'test', 'Test', $builder);
		$finder->with('foo', Operator::EQUAL, 'bar1');

		$finder->getItem();
	}
}
