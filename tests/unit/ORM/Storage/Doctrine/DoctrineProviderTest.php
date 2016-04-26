<?php
namespace Joomla\Tests\Unit\ORM\Storage\Doctrine;

use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Finder\CollectionFinderInterface;
use Joomla\ORM\Finder\EntityFinderInterface;
use Joomla\ORM\Persistor\PersistorInterface;
use Joomla\ORM\Storage\Doctrine\DoctrineProvider;

class DoctrineProviderTest extends \PHPUnit_Framework_TestCase
{

	public function testStore()
	{
		$builder = $this->getMockBuilder(EntityBuilder::class)
			->disableOriginalConstructor()
			->getMock();
		$persistor = new DoctrineProvider('sqlite::memory:', [], $builder);

		$this->assertInstanceOf(EntityFinderInterface::class, $persistor->getEntityFinder('Test'));
		$this->assertInstanceOf(CollectionFinderInterface::class, $persistor->getCollectionFinder('Test'));
		$this->assertInstanceOf(PersistorInterface::class, $persistor->getPersistor('Test'));
	}
}
