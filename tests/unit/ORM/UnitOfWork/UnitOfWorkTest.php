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
use Joomla\ORM\Repository\Repository;
use Joomla\ORM\Repository\RepositoryInterface;
use Joomla\ORM\Storage\Doctrine\DoctrineDataMapper;
use Joomla\ORM\Storage\Doctrine\DoctrineTransactor;
use Joomla\ORM\UnitOfWork\ChangeTracker;
use Joomla\Tests\Unit\ORM\Mocks\UnitOfWork;
use Joomla\Tests\Unit\ORM\Mocks\User;
use PHPUnit\Framework\TestCase;

class UnitOfWorkTest extends TestCase
{
	/** @var UnitOfWork The unit of work to use in the tests */
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

	/**
	 * Sets up the tests
	 */
	public function setUp()
	{
		$dataPath = realpath(__DIR__ . '/..');

		$this->config = parse_ini_file($dataPath . '/data/entities.doctrine.ini', true);

		$this->idAccessorRegistry = new IdAccessorRegistry();
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

		$strategy      = new RecursiveDirectoryStrategy($dataPath . '/Mocks');
		$locator       = new Locator([$strategy]);
		$this->builder = new EntityBuilder($locator, $this->config, $this->idAccessorRegistry);

		$transactor               = new DoctrineTransactor(DriverManager::getConnection(['url' => 'sqlite:///' . $dataPath . '/data/sqlite.test.db']));
		$changeTracker            = new ChangeTracker();
		$this->entityRegistry     = new EntityRegistry($this->idAccessorRegistry, $changeTracker);
		$this->unitOfWork         = new UnitOfWork(
			$this->entityRegistry,
			$this->idAccessorRegistry,
			$changeTracker,
			$transactor
		);

		$this->dataMapper = new DoctrineDataMapper(
			'User',
			'User.xml',
			$this->builder,
			'sqlite:///' . $dataPath . '/data/sqlite.test.db',
			'users'
		);
		$this->repo       = new Repository('User', $this->dataMapper, $this->idAccessorRegistry);

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
	 * Tests seeing if the unit of work picks up on an update made outside of it
	 */
	public function testCheckingIfEntityUpdateIsDetected()
	{
		$className = $this->entityRegistry->getClassName($this->entity1);
		$this->unitOfWork->registerDataMapper($className, $this->dataMapper);
		$this->entityRegistry->registerEntity($this->entity1);
		$this->entity1->setUsername("blah");
		$this->unitOfWork->checkForUpdates();
		$scheduledFoUpdate = $this->unitOfWork->getScheduledEntityUpdates();
		$this->unitOfWork->commit();
		$this->assertTrue(in_array($this->entity1, $scheduledFoUpdate));
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
	 * Tests not setting the connection
	 */
	public function testNotSettingConnection()
	{
		$this->expectException(OrmException::class);
		$unitOfWork = new UnitOfWork(
			$this->entityRegistry,
			new IdAccessorRegistry(),
			new ChangeTracker()
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
		$this->prepareDatabase();

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
				$idAccessorRegistry,
				new ChangeTracker(),
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
	 * @param \Exception $e
	 *
	 * @return string
	 */
	private function dump($e)
	{
		$msg           = '';
		$fmt           = "%s in %s(%d)\n";
		$traceAsString = '';

		while ($e instanceof \Exception)
		{
			$message       = $e->getMessage();
			$file          = $e->getFile();
			$line          = $e->getLine();
			$traceAsString = $e->getTraceAsString();
			$e             = $e->getPrevious();

			$msg .= sprintf($fmt, $message, $file, $line);
		}

		return $msg . "\n" . $traceAsString;
	}

	/**
	 * @param array $entities
	 */
	private function prepareDatabase($entities = [])
	{
		foreach ($this->dataMapper->findAll()->getItems() as $entity)
		{
			$this->dataMapper->delete($entity, $this->idAccessorRegistry);
		}

		foreach ($entities as $entity)
		{
			$this->dataMapper->insert($entity, $this->idAccessorRegistry);
		}
	}
}
