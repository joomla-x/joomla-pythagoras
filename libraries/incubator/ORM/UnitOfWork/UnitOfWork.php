<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\UnitOfWork;

use Joomla\ORM\Entity\EntityRegistry;
use Joomla\ORM\Entity\EntityStates;
use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\IdAccessorRegistry;
use Joomla\ORM\Storage\DataMapperInterface;

/**
 * Defines a unit of work that tracks changes made to entities and atomically persists them
 */
class UnitOfWork implements UnitOfWorkInterface
{
	/** @var TransactionInterface The transactor to use in our unit of work */
	private $transactor = null;

	/** @var EntityRegistry What manages/tracks entities for our unit of work */
	private $entityRegistry = null;

	/** @var IdAccessorRegistry The Id accessor registry */
	private $idAccessorRegistry = null;

	/** @var ChangeTracker The change tracker */
	private $changeTracker = null;

	/** @var array The mapping of class names to their data mappers */
	private $dataMappers = [];

	/** @var array The list of entities scheduled for insertion */
	private $scheduledForInsertion = [];

	/** @var array The list of entities scheduled for update */
	private $scheduledForUpdate = [];

	/** @var array The list of entities scheduled for deletion */
	private $scheduledForDeletion = [];

	/**
	 * @param EntityRegistry       $entityRegistry     The entity registry to use
	 * @param IdAccessorRegistry   $idAccessorRegistry The Id accessor registry to use
	 * @param ChangeTracker        $changeTracker      The change tracker to use
	 * @param TransactionInterface $connection         The transactor to use in our unit of work
	 */
	public function __construct(
		EntityRegistry $entityRegistry,
		IdAccessorRegistry $idAccessorRegistry,
		ChangeTracker $changeTracker,
		TransactionInterface $connection = null
	)
	{
		$this->entityRegistry     = $entityRegistry;
		$this->idAccessorRegistry = $idAccessorRegistry;
		$this->changeTracker      = $changeTracker;

		if ($connection !== null)
		{
			$this->setTransactor($connection);
		}
	}

	/**
	 * Commits any entities that have been scheduled for insertion/updating/deletion
	 *
	 * @throws OrmException Thrown if there was an error committing the transaction
	 */
	public function commit()
	{
		if (!$this->transactor instanceof TransactionInterface)
		{
			throw new OrmException("Connection not set");
		}

		$this->checkForUpdates();
		$this->preCommit();
		$this->transactor->beginTransaction();

		try
		{
			$this->insert();
			$this->update();
			$this->delete();
			$this->transactor->commit();
		}
		catch (\Exception $e)
		{
			$this->transactor->rollBack();
			$this->postRollback();
			throw new OrmException("Commit failed.\n" . $e->getMessage(), 0, $e);
		}

		$this->postCommit();

		// Clear our schedules
		$this->scheduledForInsertion = [];
		$this->scheduledForUpdate    = [];
		$this->scheduledForDeletion  = [];
		$this->entityRegistry->clearAggregateRoots();
	}

	/**
	 * Detaches an entity from being managed
	 *
	 * @param object $entity The entity to detach
	 */
	public function detach($entity)
	{
		$this->entityRegistry->deregisterEntity($entity);
		$objectHashId = $this->entityRegistry->getObjectHashId($entity);
		unset($this->scheduledForInsertion[$objectHashId]);
		unset($this->scheduledForUpdate[$objectHashId]);
		unset($this->scheduledForDeletion[$objectHashId]);
	}

	/**
	 * Disposes of all data in this unit of work
	 */
	public function dispose()
	{
		$this->scheduledForInsertion = [];
		$this->scheduledForUpdate    = [];
		$this->scheduledForDeletion  = [];
		$this->entityRegistry->clearAggregateRoots();
		$this->entityRegistry->clear();
	}

	/**
	 * Gets the unit of work's entity registry
	 *
	 * @return EntityRegistry The entity registry used by the unit of work
	 */
	public function getEntityRegistry()
	{
		return $this->entityRegistry;
	}

	/**
	 * Registers a data mapper for a class
	 * Registering a data mapper for a class will overwrite any previously-set data mapper for that class
	 *
	 * @param string              $className  The name of the class whose data mapper we're registering
	 * @param DataMapperInterface $dataMapper The data mapper for the class
	 */
	public function registerDataMapper($className, DataMapperInterface $dataMapper)
	{
		$this->dataMappers[$className] = $dataMapper;
	}

	/**
	 * Schedules an entity for deletion
	 *
	 * @param object $entity The entity to schedule for deletion
	 */
	public function scheduleForDeletion($entity)
	{
		$this->scheduledForDeletion[$this->entityRegistry->getObjectHashId($entity)] = $entity;
	}

	/**
	 * Schedules an entity for insertion
	 *
	 * @param object $entity The entity to schedule for insertion
	 */
	public function scheduleForInsertion($entity)
	{
		$objectHashId                               = $this->entityRegistry->getObjectHashId($entity);
		$this->scheduledForInsertion[$objectHashId] = $entity;
		$this->entityRegistry->setState($entity, EntityStates::QUEUED);
	}

	/**
	 * Schedules an entity for update
	 *
	 * @param object $entity The entity to schedule for update
	 */
	public function scheduleForUpdate($entity)
	{
		$this->scheduledForUpdate[$this->entityRegistry->getObjectHashId($entity)] = $entity;
	}

	/**
	 * @inheritdoc
	 */
	public function setTransactor(TransactionInterface $transactor)
	{
		$this->transactor = $transactor;
	}

	/**
	 * Checks for any changes made to entities, and if any are found, they're scheduled for update
	 *
	 */
	protected function checkForUpdates()
	{
		$managedEntities = $this->entityRegistry->getEntities();

		foreach ($managedEntities as $entity)
		{
			$objectHashId = $this->entityRegistry->getObjectHashId($entity);

			if ($this->entityRegistry->isRegistered($entity)
			    && !isset($this->scheduledForInsertion[$objectHashId])
			    && !isset($this->scheduledForUpdate[$objectHashId])
			    && !isset($this->scheduledForDeletion[$objectHashId])
			    && $this->changeTracker->hasChanged($entity)
			)
			{
				$this->scheduleForUpdate($entity);
			}
		}
	}

	/**
	 * Attempts to update all the entities scheduled for deletion
	 */
	protected function delete()
	{
		foreach ($this->scheduledForDeletion as $objectHashId => $entity)
		{
			$dataMapper = $this->getDataMapper($this->entityRegistry->getClassName($entity));
			$dataMapper->delete($entity, $this->idAccessorRegistry);
			// Order here matters
			$this->detach($entity);
			$this->entityRegistry->setState($entity, EntityStates::DEQUEUED);
		}
	}

	/**
	 * Gets the data mapper for the input class
	 *
	 * @param string $className The name of the class whose data mapper we're searching for
	 *
	 * @return DataMapperInterface The data mapper for the input class
	 * @throws \RuntimeException Thrown if there was no data mapper for the input class name
	 */
	protected function getDataMapper($className)
	{
		if (!isset($this->dataMappers[$className]))
		{
			throw new \RuntimeException("No data mapper for $className");
		}

		return $this->dataMappers[$className];
	}

	/**
	 * Gets the list of entities that are scheduled for deletion
	 *
	 * @return object[] The list of entities scheduled for deletion
	 */
	protected function getScheduledEntityDeletions()
	{
		return array_values($this->scheduledForDeletion);
	}

	/**
	 * Gets the list of entities that are scheduled for insertion
	 *
	 * @return object[] The list of entities scheduled for insertion
	 */
	protected function getScheduledEntityInsertions()
	{
		return array_values($this->scheduledForInsertion);
	}

	/**
	 * Gets the list of entities that are scheduled for update
	 *
	 * @return object[] The list of entities scheduled for update
	 */
	protected function getScheduledEntityUpdates()
	{
		return array_values($this->scheduledForUpdate);
	}

	/**
	 * Attempts to insert all the entities scheduled for insertion
	 */
	protected function insert()
	{
		foreach ($this->scheduledForInsertion as $objectHashId => $entity)
		{
			// If this entity was a child of aggregate roots, then call its methods to set the aggregate root Id
			$this->entityRegistry->runAggregateRootCallbacks($entity);
			$className  = $this->entityRegistry->getClassName($entity);
			$dataMapper = $this->getDataMapper($className);

			$dataMapper->insert($entity, $this->idAccessorRegistry);

			$this->entityRegistry->registerEntity($entity);
		}
	}

	/**
	 * Performs any actions after the commit
	 */
	protected function postCommit()
	{
		// Leave blank for extending classes to implement
	}

	/**
	 * Performs any actions after a rollback
	 */
	protected function postRollback()
	{
		// Unset each of the new entities' Ids
		foreach ($this->scheduledForInsertion as $objectHashId => $entity)
		{
			$this->idAccessorRegistry->setEntityId($entity, null);
		}
	}

	/**
	 * Performs any actions before a commit
	 */
	protected function preCommit()
	{
		// Leave blank for extending classes to implement
	}

	/**
	 * Attempts to update all the entities scheduled for updating
	 */
	protected function update()
	{
		foreach ($this->scheduledForUpdate as $objectHashId => $entity)
		{
			// If this entity was a child of aggregate roots, then call its methods to set the aggregate root Id
			$this->entityRegistry->runAggregateRootCallbacks($entity);
			$dataMapper = $this->getDataMapper($this->entityRegistry->getClassName($entity));
			$dataMapper->update($entity, $this->idAccessorRegistry);
			$this->entityRegistry->registerEntity($entity);
		}
	}
}
