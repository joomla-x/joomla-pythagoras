<?php
/**
 * Part of the Joomla Framework ORM Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\ORM\UnitOfWork;

use Doctrine\DBAL\DriverManager;
use Joomla\ORM\Definition\Locator\Locator;
use Joomla\ORM\Definition\Locator\Strategy\RecursiveDirectoryStrategy;
use Joomla\ORM\Entity\EntityBuilder;
use Joomla\ORM\Entity\EntityRegistry;
use Joomla\ORM\Entity\EntityStates;
use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\IdAccessorRegistry;
use Joomla\ORM\Repository\RepositoryInterface;
use Joomla\ORM\Service\RepositoryFactory;
use Joomla\ORM\Storage\Doctrine\DoctrineDataMapper;
use Joomla\ORM\Storage\Doctrine\DoctrineTransactor;
use Joomla\ORM\UnitOfWork\TransactionInterface;
use Joomla\ORM\UnitOfWork\UnitOfWork;
use Joomla\Tests\Unit\DumpTrait;
use Joomla\Tests\Unit\ORM\Mocks\UnitOfWorkAccessDecorator;
use Joomla\Tests\Unit\ORM\Mocks\User;
use PHPUnit\Framework\TestCase;

class UnitOfWorkTest extends TestCase
{
	/** @var UnitOfWorkAccessDecorator The unit of work to use in the tests */
	private $unitOfWork = null;

	/** @var EntityRegistry The entity registry to use in tests */
	private $entityRegistry = null;

	/** @var DoctrineDataMapper The data mapper to use in tests */
	private $dataMapper = null;

	/** @var User An entity to use in the tests */
	private $entity1 = null;

	/** @var User An entity to use in the tests */
	private $entity2 = null;

	/** @var User An entity to use in the tests */
	private $entity3 = null;

	/** @var  array */
	protected $config;

	/** @var  RepositoryInterface */
	protected $repo;

	/** @var EntityBuilder The entity builder */
	protected $builder;

	/** @var  IdAccessorRegistry */
	protected $idAccessorRegistry;

	/** @var  TransactionInterface */
	protected $transactor;

	use DumpTrait;

	/**
	 * Sets up the tests
	 */
	public function setUp()
	{
		$dataPath = realpath(__DIR__ . '/..');

		$this->config = parse_ini_file($dataPath . '/data/entities.doctrine.ini', true);

		$connection               = DriverManager::getConnection(['url' => $this->config['databaseUrl']]);
		$this->transactor         = new DoctrineTransactor($connection);
		$this->idAccessorRegistry = new IdAccessorRegistry;

		$repositoryFactory = new RepositoryFactory($this->config, $this->transactor);
		$this->idAccessorRegistry->registerIdAccessors(
			User::class,
			function (User $user)
			{
				return $user->getId();
			},
			function (User $user, $id)
			{
				$user->setId($id);
			}
		);

		$strategy      = new RecursiveDirectoryStrategy($this->config['definitionPath']);
		$locator       = new Locator([$strategy]);
		$this->builder = new EntityBuilder($locator, $this->config, $repositoryFactory);

		$this->repo           = $repositoryFactory->forEntity(User::class);
		$this->entityRegistry = $repositoryFactory->getEntityRegistry();
		$this->unitOfWork     = new UnitOfWorkAccessDecorator($repositoryFactory->getUnitOfWork());
		$this->dataMapper     = $this->unitOfWork->getDataMapper(User::class);

		/**
		 * The Ids are purposely unique so that we can identify them as such without having to first insert them to
		 * assign unique Ids
		 * They are also purposely set to 724, 1987, and 345 so that they won't potentially overlap with any default
		 * values set to the Ids
		 */
		$this->entity1 = new User(724, "foo");
		$this->entity2 = new User(1987, "bar");
		$this->entity3 = new User(345, "baz");
	}

	/**
	 * @testdox An entity is registered automatically when added to the repository
	 */
	public function testAddingAnEntityToRepoAutomaticallyRegistersIt()
	{
		$this->prepareDatabase();
		$this->repo->add($this->entity1);

		$this->assertTrue($this->entityRegistry->isRegistered($this->entity1));
	}

	/**
	 * @testdox The UnitOfWork detects an update made outside of it
	 */
	public function testChangesAreDetected()
	{
		$this->prepareDatabase();
		$this->repo->add($this->entity1);

		$this->assertContains($this->entity1, $this->unitOfWork->getScheduledEntityInsertions(), "Entity should have been scheduled for insertion");
		$this->assertNotContains($this->entity1, $this->unitOfWork->getScheduledEntityUpdates(), "Entity should not have been scheduled for update");

		$this->entity1->setUsername("blah");

		$this->unitOfWork->checkForUpdates();

		/*
		 * This is not a change, since the entity has not yet been inserted!
		 */
		$this->assertContains($this->entity1, $this->unitOfWork->getScheduledEntityInsertions(), "Entity should still be scheduled for insertion");
		$this->assertNotContains($this->entity1, $this->unitOfWork->getScheduledEntityUpdates(), "Entity should still not be scheduled for update");

		$this->unitOfWork->commit();

		$this->entity1->setUsername("blub");

		$this->unitOfWork->checkForUpdates();

		$this->assertNotContains($this->entity1, $this->unitOfWork->getScheduledEntityInsertions(), "Entity should not longer be scheduled for insertion");
		$this->assertContains($this->entity1, $this->unitOfWork->getScheduledEntityUpdates(), "Entity should now be scheduled for update");
	}

	/**
	 * Tests checking if an entity update is detected after copying its pointer to another variable
	 */
	public function testCheckingIfEntityUpdateIsDetectedAfterCopyingPointer()
	{
		$foo = $this->getInsertedEntity();
		$bar = $foo;
		$bar->setUsername("bar");
		$this->unitOfWork->commit();

		$this->assertEquals($bar, $this->dataMapper->getById($foo->getId()));
	}

	/**
	 * Tests checking if an entity update is detected after it is returned by a function
	 */
	public function testCheckingIfEntityUpdateIsDetectedAfterReturningFromFunction()
	{
		$this->prepareDatabase();

		$foo = $this->getInsertedEntity();
		$foo->setUsername("bar");
		$this->unitOfWork->commit();

		$this->assertEquals($foo, $this->dataMapper->getById($foo->getId()));
	}

	/**
	 * Tests detaching a registered entity after scheduling it for deletion, insertion, and update
	 */
	public function testDetachingEntityAfterSchedulingForDeletionInsertionUpdate()
	{
		$this->entityRegistry->registerEntity($this->entity1);
		$this->unitOfWork->scheduleForDeletion($this->entity1);
		$this->unitOfWork->scheduleForInsertion($this->entity1);
		$this->unitOfWork->scheduleForUpdate($this->entity1);
		$this->unitOfWork->detach($this->entity1);
		$this->assertFalse($this->entityRegistry->isRegistered($this->entity1));
		$this->assertEquals(EntityStates::UNREGISTERED, $this->entityRegistry->getEntityState($this->entity1));
		$this->assertFalse(in_array($this->entity1, $this->unitOfWork->getScheduledEntityDeletions()));
		$this->assertFalse(in_array($this->entity1, $this->unitOfWork->getScheduledEntityInsertions()));
		$this->assertFalse(in_array($this->entity1, $this->unitOfWork->getScheduledEntityUpdates()));
	}

	/**
	 * Tests disposing of the unit of work
	 */
	public function testDisposing()
	{
		$this->entityRegistry->registerEntity($this->entity1);
		$this->unitOfWork->dispose();
		$this->assertFalse($this->entityRegistry->isRegistered($this->entity1));
		$this->assertEquals(EntityStates::NEVER_REGISTERED, $this->entityRegistry->getEntityState($this->entity1));
		$this->assertEquals([], $this->unitOfWork->getScheduledEntityDeletions());
		$this->assertEquals([], $this->unitOfWork->getScheduledEntityInsertions());
		$this->assertEquals([], $this->unitOfWork->getScheduledEntityUpdates());
	}

	/**
	 * Tests getting the entity registry
	 */
	public function testGettingEntityRegistry()
	{
		$this->assertSame($this->entityRegistry, $this->unitOfWork->getEntityRegistry());
	}

	/**
	 * Tests inserting and deleting an entity in a single transaction
	 */
	public function testInsertingAndDeletingEntity()
	{
		$className = $this->entityRegistry->getClassName($this->entity1);
		$this->unitOfWork->registerDataMapper($className, $this->dataMapper);
		$this->entityRegistry->registerEntity($this->entity1);
		$this->unitOfWork->scheduleForInsertion($this->entity1);
		$this->unitOfWork->scheduleForDeletion($this->entity1);
		$this->unitOfWork->commit();
		$this->assertFalse($this->entityRegistry->isRegistered($this->entity1));
		$this->assertEquals(EntityStates::DEQUEUED, $this->entityRegistry->getEntityState($this->entity1));
		$this->expectException(OrmException::class);
		$this->dataMapper->getById($this->entity1->getId());
	}

	/**
	 * Tests making sure an unchanged registered entity isn't scheduled for update
	 */
	public function testMakingSureUnchangedEntityIsNotScheduledForUpdate()
	{
		$className = $this->entityRegistry->getClassName($this->entity1);
		$this->unitOfWork->registerDataMapper($className, $this->dataMapper);
		$this->entityRegistry->registerEntity($this->entity1);
		$this->unitOfWork->checkForUpdates();
		$scheduledFoUpdate = $this->unitOfWork->getScheduledEntityUpdates();
		$this->assertFalse(in_array($this->entity1, $scheduledFoUpdate));
	}

	/**
	 * Tests not setting the transactor
	 */
	public function testNotSettingConnection()
	{
		$this->expectException(OrmException::class);
		$unitOfWork = new UnitOfWork(
			$this->entityRegistry
		);
		$unitOfWork->commit();
	}

	/**
	 * Tests scheduling a deletion for an entity
	 */
	public function testSchedulingDeletionEntity()
	{
		$this->prepareDatabase([$this->entity1]);

		$this->unitOfWork->registerDataMapper($this->entityRegistry->getClassName($this->entity1), $this->dataMapper);
		$this->unitOfWork->scheduleForDeletion($this->entity1);
		$this->unitOfWork->checkForUpdates();
		$scheduledFoDeletion = $this->unitOfWork->getScheduledEntityDeletions();
		$this->unitOfWork->commit();

		$this->assertTrue(in_array($this->entity1, $scheduledFoDeletion));
		$this->assertFalse($this->entityRegistry->isRegistered($this->entity1));
		$this->assertEquals(EntityStates::DEQUEUED, $this->entityRegistry->getEntityState($this->entity1));

		$this->expectException(OrmException::class);
		$this->dataMapper->getById($this->entity1->getId());
	}

	/**
	 * Tests scheduling an insertion for an entity
	 */
	public function testSchedulingInsertionEntity()
	{
		$className = $this->entityRegistry->getClassName($this->entity1);
		$this->unitOfWork->registerDataMapper($className, $this->dataMapper);
		$this->unitOfWork->scheduleForInsertion($this->entity1);
		$this->assertEquals(EntityStates::QUEUED, $this->entityRegistry->getEntityState($this->entity1));
		$this->unitOfWork->checkForUpdates();
		$scheduledFoInsertion = $this->unitOfWork->getScheduledEntityInsertions();
		$this->unitOfWork->commit();

		$this->assertTrue(in_array($this->entity1, $scheduledFoInsertion));

		$this->builder->resolve($this->entity1);
		$this->assertEquals($this->entity1, $this->entityRegistry->getEntity($className, $this->entity1->getId()));
		$this->assertEquals(EntityStates::REGISTERED, $this->entityRegistry->getEntityState($this->entity1));
		$this->assertEquals($this->entity1, $this->dataMapper->getById($this->entity1->getId()));
		$this->assertEquals(724, $this->entity1->getId());
	}

	/**
	 * Tests scheduling an update for an entity
	 */
	public function testSchedulingUpdate()
	{
		$className = $this->entityRegistry->getClassName($this->entity1);
		$this->unitOfWork->registerDataMapper($className, $this->dataMapper);
		$this->unitOfWork->scheduleForUpdate($this->entity1);
		$this->entity1->setUsername("blah");
		$this->unitOfWork->checkForUpdates();
		$scheduledFoUpdate = $this->unitOfWork->getScheduledEntityUpdates();
		$this->unitOfWork->commit();

		$this->assertTrue(in_array($this->entity1, $scheduledFoUpdate));

		$this->builder->resolve($this->entity1);
		$this->assertEquals($this->entity1, $this->entityRegistry->getEntity($className, $this->entity1->getId()));
		$this->assertEquals(EntityStates::REGISTERED, $this->entityRegistry->getEntityState($this->entity1));
		$this->assertEquals($this->entity1, $this->dataMapper->getById($this->entity1->getId()));
	}

	/**
	 * Tests setting the aggregate root on inserted entities
	 */
	public function testSettingAggregateRootOnInsertedEntities()
	{
		$this->prepareDatabase();

		$originalAggregateRootId = $this->entity2->getAggregateRootId();
		$className               = $this->entityRegistry->getClassName($this->entity1);
		$this->unitOfWork->registerDataMapper($className, $this->dataMapper);
		$this->unitOfWork->scheduleForInsertion($this->entity1);
		$this->unitOfWork->scheduleForInsertion($this->entity2);
		$this->unitOfWork->getEntityRegistry()->registerAggregateRootCallback($this->entity1, $this->entity2,
			function (User $aggregateRoot, User $child)
			{
				$child->setAggregateRootId($aggregateRoot->getId());
			});
		$this->unitOfWork->commit();

		$this->assertNotEquals($originalAggregateRootId, $this->entity2->getAggregateRootId());
		$this->assertEquals($this->entity1->getId(), $this->entity2->getAggregateRootId());
	}

	/**
	 * Tests setting the aggregate root on updated entities
	 */
	public function testSettingAggregateRootOnUpdatedEntities()
	{
		$this->prepareDatabase([$this->entity2]);

		$originalAggregateRootId = $this->entity2->getAggregateRootId();
		$className               = $this->entityRegistry->getClassName($this->entity1);
		$this->unitOfWork->registerDataMapper($className, $this->dataMapper);
		$this->unitOfWork->scheduleForInsertion($this->entity1);
		$this->unitOfWork->scheduleForUpdate($this->entity2);
		$this->unitOfWork->getEntityRegistry()->registerAggregateRootCallback($this->entity1, $this->entity2,
			function (User $aggregateRoot, User $child)
			{
				$child->setAggregateRootId($aggregateRoot->getId());
			});
		$this->unitOfWork->commit();

		$this->assertNotEquals($originalAggregateRootId, $this->entity2->getAggregateRootId());
		$this->assertEquals($this->entity1->getId(), $this->entity2->getAggregateRootId());
	}

	/**
	 * Tests to make sure that an entity's Id is being set after it's committed
	 */
	public function testThatEntityIdIsBeingSetAfterCommit()
	{
		$foo = $this->getInsertedEntity();
		$this->assertEquals(18175, $foo->getId());
	}

	/**
	 * Tests an unsuccessful commit
	 */
	public function testUnsuccessfulCommit()
	{
		$exceptionThrown    = false;
		$idAccessorRegistry = new IdAccessorRegistry();
		$idAccessorRegistry->registerIdAccessors(
			User::class,
			function (User $user)
			{
				return $user->getId();
			},
			function (User $user, $id)
			{
				$user->setId($id);
			}
		);
		try
		{
			$connection       = null;
			$this->unitOfWork = new UnitOfWork(
				$this->entityRegistry,
				$connection
			);
			$this->entity1    = new User(1, "foo");
			$this->entity2    = new User(2, "bar");
			$className        = $this->entityRegistry->getClassName($this->entity1);
			$this->unitOfWork->registerDataMapper($className, $this->dataMapper);
			$this->unitOfWork->scheduleForInsertion($this->entity1);
			$this->unitOfWork->scheduleForInsertion($this->entity2);
			$this->unitOfWork->commit();
		}
		catch (OrmException $e)
		{
			$exceptionThrown = true;
		}

		$this->assertTrue($exceptionThrown);
		$this->assertEquals(1, $this->entity1->getId());
		$this->assertEquals(2, $this->entity2->getId());
	}

	/**
	 * Gets the entity after committing it
	 *
	 * @return User The entity from the data mapper
	 * @throws OrmException Thrown if there was an error committing the transaction
	 */
	private function getInsertedEntity()
	{
		$className = $this->entityRegistry->getClassName($this->entity1);
		$this->unitOfWork->registerDataMapper($className, $this->dataMapper);
		$foo = new User(18175, "blah");
		$this->builder->resolve($foo);
		$this->unitOfWork->scheduleForInsertion($foo);
		$this->unitOfWork->commit();

		return $this->entityRegistry->getEntity($className, $foo->getId());
	}

	/**
	 * @param array $entities
	 */
	private function prepareDatabase($entities = [])
	{
		foreach ($this->dataMapper->findAll()->getItems() as $entity)
		{
			$this->dataMapper->delete($entity);
		}

		foreach ($entities as $entity)
		{
			$this->dataMapper->insert($entity);
		}
	}
}
