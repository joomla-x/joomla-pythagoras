<?php

namespace Joomla\Tests\Unit\Cms\ServiceProvider;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\ORM\Service\RepositoryFactory;
use Joomla\ORM\Service\StorageServiceProvider;

class StorageServiceProviderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @testdox The StorageServiceProvider implements the ServiceProviderInterface
	 */
	public function testTheStorageServiceProviderImplementsTheServiceProviderInterface()
	{
		$this->assertInstanceOf(ServiceProviderInterface::class, new StorageServiceProvider());
	}

	/**
	 * @testdox The StorageServiceProvider adds a RepositoryFactory to a container
	 */
	public function testStorageServiceProviderCreatesStorage()
	{
		$container = new Container();

		$service = new StorageServiceProvider();
		$service->register($container);

		$this->assertInstanceOf(RepositoryFactory::class, $container->get('Repository'));
	}

	/**
	 * @testdox The StorageServiceProvider adds an Storage to a container with an alias
	 */
	public function testStorageServiceProviderCreatesStorageWithAlias()
	{
		$container = new Container();

		$service = new StorageServiceProvider();
		$service->register($container, 'unit');

		$this->assertInstanceOf(RepositoryFactory::class, $container->get('unit'));
	}
}
