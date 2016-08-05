<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\UnitOfWork;

use Joomla\ORM\Definition\Parser\BelongsTo;
use Joomla\ORM\Definition\Parser\HasMany;
use Joomla\ORM\Definition\Parser\HasManyThrough;
use Joomla\ORM\Definition\Parser\HasOne;
use Joomla\ORM\Entity\EntityRegistry;
use Joomla\ORM\Entity\EntityStates;
use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\Storage\DataMapperInterface;
use ReflectionClass;

/**
 * Defines a unit of work that tracks changes made to entities and atomically persists them
 *
 * @package  Joomla/ORM
 *
 * @since    __DEPLOY_VERSION__
 */
class UnitOfWork implements UnitOfWorkInterface
{
	/** @var TransactionInterface The transactor to use in our unit of work */
	private $transactor = null;

	/** @var EntityRegistry What manages/tracks entities for our unit of work */
	private $entityRegistry = null;

	/** @var array The mapping of class names to their data mappers */
	private $dataMappers = [];

	/** @var array The list of entities scheduled for insertion */
	private $scheduledForInsertion = [];

	/** @var array The list of entities scheduled for update */
	private $scheduledForUpdate = [];

	/** @var array The list of entities scheduled for deletion */
	private $scheduledForDeletion = [];

	/**
	 * @param   EntityRegistry        $entityRegistry  The entity registry to use
	 * @param   TransactionInterface  $connection      The transactor to use in our unit of work
	 */
	public function __construct(EntityRegistry $entityRegistry, TransactionInterface $connection = null)
	{
		$this->entityRegistry = $entityRegistry;

		if ($connection !== null)
		{
			$this->setTransactor($connection);
		}
	}

	/**
	 * Commits any entities that have been scheduled for insertion/updating/deletion
	 *
	 * @return  void
	 * @throws  OrmException  if there was an error committing the transaction
	 */
	public function commit()
	{
		if (!$this->transactor instanceof TransactionInterface)
		{
			throw new OrmException("Connection not set");
		}

		$this->checkForChangedRelations();
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
	 * @param   object  $entity  The entity to detach
	 *
	 * @return  void
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
	 *
	 * @return  void
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
	 * @return  EntityRegistry  The entity registry used by the unit of work
	 */
	public function getEntityRegistry()
	{
		return $this->entityRegistry;
	}

	/**
	 * Registers a data mapper for a class
	 * Registering a data mapper for a class will overwrite any previously-set data mapper for that class
	 *
	 * @param   string                $className   The name of the class whose data mapper we're registering
	 * @param   DataMapperInterface   $dataMapper  The data mapper for the class
	 *
	 * @return  void
	 */
	public function registerDataMapper($className, DataMapperInterface $dataMapper)
	{
		$this->dataMappers[$className] = $dataMapper;
	}

	/**
	 * Schedules an entity for deletion
	 *
	 * @param   object  $entity  The entity to schedule for deletion
	 *
	 * @return  void
	 */
	public function scheduleForDeletion($entity)
	{
		$this->scheduledForDeletion[$this->entityRegistry->getObjectHashId($entity)] = $entity;
	}

	/**
	 * Schedules an entity for insertion
	 *
	 * @param   object  $entity  The entity to schedule for insertion
	 *
	 * @return  void
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
	 * @param   object  $entity  The entity to schedule for update
	 *
	 * @return  void
	 */
	public function scheduleForUpdate($entity)
	{
		$this->scheduledForUpdate[$this->entityRegistry->getObjectHashId($entity)] = $entity;
	}

	/**
	 * Sets the database transactor
	 *
	 * @param   TransactionInterface  $transactor  The transactor to use
	 *
	 * @return  void
	 */
	public function setTransactor(TransactionInterface $transactor)
	{
		$this->transactor = $transactor;
	}

	/**
	 * Checks for changed children
	 *
	 * @return  void
	 */
	protected function checkForChangedRelations()
	{
		foreach ($this->entityRegistry->getEntities() as $entity)
		{
			$meta         = $this->entityRegistry->getMeta($entity);
			$objectHashId = $this->entityRegistry->getObjectHashId($entity);

			foreach ($meta->relations['belongsTo'] as $field => $relation)
			{
			}

			foreach ($meta->relations['hasOne'] as $field => $relation)
			{
				/*
				 * We are handling a Detail and checking the Extra.
				 * Extra->detailId = Detail->id
				 */
				$varObjName = $relation->varObjectName();
				$varReferenceName = $relation->varReferenceName();

				$currentRelation = $this->getValue($entity, $varObjName);
				$originalRelation = $this->getValue($this->entityRegistry->getOriginal($entity), $varObjName);

				if (empty($currentRelation))
				{
					// Current entity has no relation
					if (!empty($originalRelation))
					{
						// Original had a relation, so relation was removed
						$this->scheduleForDeletion($originalRelation);
					}

					continue;
				}

				// Current entity has a relation
				$currentRelationHash = $this->entityRegistry->getObjectHashId($currentRelation);

				if (isset($this->scheduledForDeletion[$objectHashId]) && $relation->cascadeDelete())
				{
					$this->scheduleForDeletion($currentRelation);
				}

				if (empty($originalRelation))
				{
					// Original had no relation, so relation was newly created
					$this->scheduleRelationForInsertion($entity, $currentRelation, $varReferenceName);

					continue;
				}

				$originalRelationHash = $this->entityRegistry->getObjectHashId($originalRelation);

				if ($currentRelationHash != $originalRelationHash)
				{
					// Relation was exchanged
					$this->scheduleForDeletion($originalRelation);
					$this->scheduleRelationForInsertion($entity, $currentRelation, $varReferenceName);

					continue;
				}
			}

			foreach ($meta->relations['hasMany'] as $field => $relation)
			{
				/*
				 * We are handling a Master and checking the Details.
				 * Details[i]->masterId = Master->id
				 * or
				 * We are handling a Master and checking the children.
				 * Children[i]->parentId = Master->id
				 */
			}

			foreach ($meta->relations['hasManyThrough'] as $field => $relation)
			{
				/*
				 * We are handling a Master and checking the Tags.
				 * Map[i]->masterId = Master->id
				 * Map[i]->tagId = Tag->id
				 */
			}
		}
	}

	/**
	 * Checks for any changes made to entities, and if any are found, they're scheduled for update
	 *
	 * @return  void
	 */
	protected function checkForUpdates()
	{
		foreach ($this->entityRegistry->getEntities() as $entity)
		{
			$objectHashId = $this->entityRegistry->getObjectHashId($entity);

			if ($this->entityRegistry->isRegistered($entity) && !$this->isScheduled($objectHashId) && $this->entityRegistry->hasChanged($entity))
			{
				$this->scheduleForUpdate($entity);
			}
		}
	}

	/**
	 * Attempts to update all the entities scheduled for deletion
	 *
	 * @return  void
	 */
	protected function delete()
	{
		foreach ($this->scheduledForDeletion as $objectHashId => $entity)
		{
			$dataMapper = $this->getDataMapper($this->entityRegistry->getClassName($entity));
			$dataMapper->delete($entity);

			// Order here matters
			$this->detach($entity);
			$this->entityRegistry->setState($entity, EntityStates::DEQUEUED);
		}
	}

	/**
	 * Gets the data mapper for the input class
	 *
	 * @param   string  $className  The name of the class whose data mapper we're searching for
	 *
	 * @return  DataMapperInterface  The data mapper for the input class
	 * @throws \ RuntimeException Thrown if there was no data mapper for the input class name
	 */
	public function getDataMapper($className)
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
	 * @return  object[]  The list of entities scheduled for deletion
	 */
	protected function getScheduledEntityDeletions()
	{
		return array_values($this->scheduledForDeletion);
	}

	/**
	 * Gets the list of entities that are scheduled for insertion
	 *
	 * @return  object[]  The list of entities scheduled for insertion
	 */
	protected function getScheduledEntityInsertions()
	{
		return array_values($this->scheduledForInsertion);
	}

	/**
	 * Gets the list of entities that are scheduled for update
	 *
	 * @return  object[]  The list of entities scheduled for update
	 */
	protected function getScheduledEntityUpdates()
	{
		return array_values($this->scheduledForUpdate);
	}

	/**
	 * Attempts to insert all the entities scheduled for insertion
	 *
	 * @return  void
	 */
	protected function insert()
	{
		foreach ($this->scheduledForInsertion as $objectHashId => $entity)
		{
			// If this entity was a child of aggregate roots, then call its methods to set the aggregate root Id
			$this->entityRegistry->runAggregateRootCallbacks($entity);
			$className  = $this->entityRegistry->getClassName($entity);
			$dataMapper = $this->getDataMapper($className);

			$dataMapper->insert($entity);

			$this->entityRegistry->registerEntity($entity);
		}
	}

	/**
	 * Performs any actions after the commit
	 *
	 * @return  void
	 */
	protected function postCommit()
	{
		// Leave blank for extending classes to implement
	}

	/**
	 * Performs any actions after a rollback
	 *
	 * @return  void
	 */
	protected function postRollback()
	{
		// Unset each of the new entities' Ids
		foreach ($this->scheduledForInsertion as $objectHashId => $entity)
		{
			$this->entityRegistry->setEntityId($entity, null);
		}
	}

	/**
	 * Performs any actions before a commit
	 *
	 * @return  void
	 */
	protected function preCommit()
	{
		// Leave blank for extending classes to implement
	}

	/**
	 * Attempts to update all the entities scheduled for updating
	 *
	 * @return  void
	 */
	protected function update()
	{
		foreach ($this->scheduledForUpdate as $objectHashId => $entity)
		{
			// If this entity was a child of aggregate roots, then call its methods to set the aggregate root Id
			$this->entityRegistry->runAggregateRootCallbacks($entity);
			$dataMapper = $this->getDataMapper($this->entityRegistry->getClassName($entity));
			$dataMapper->update($entity);
			$this->entityRegistry->registerEntity($entity);
		}
	}

	/**
	 * Checks if an object is scheduled
	 *
	 * @param   string  $objectHashId  The object's hash
	 *
	 * @return  boolean
	 */
	protected function isScheduled($objectHashId)
	{
		return isset($this->scheduledForInsertion[$objectHashId])
			|| isset($this->scheduledForUpdate[$objectHashId])
			|| isset($this->scheduledForDeletion[$objectHashId]);
	}

	/**
	 * Schedules an entity for insertion creating an aggregate root callback
	 *
	 * @param   object  $root        The root object
	 * @param   object  $child       The child object (containing the foreign key)
	 * @param   string  $foreignKey  The name of the foreign key property in the child object
	 *
	 * @return  void
	 */
	protected function scheduleRelationForInsertion($root, $child, $foreignKey)
	{
		if (!$this->entityRegistry->isRegistered($child))
		{
			$this->scheduleForInsertion($child);
		}

		$isAccessorRegistry = $this->entityRegistry->getIdAccessorRegistry();
		$this->entityRegistry->registerAggregateRootCallback(
			$root,
			$child,
			function ($root, $child) use ($foreignKey, $isAccessorRegistry) {
				$reflection = new \ReflectionProperty(get_class($child), $foreignKey);
				$reflection->setAccessible(true);
				$reflection->setValue($child, $isAccessorRegistry->getEntityId($root));
			}
		);
	}

	/**
	 * Gets a value from an object
	 *
	 * @param   object $object   The object
	 * @param   string $property The property
	 *
	 * @return mixed
	 */
	protected function getValue($object, $property)
	{
		if (isset($object->{$property}))
		{
			return $object->{$property};
		}

		$reflectionClass = new ReflectionClass($object);

		if (!$reflectionClass->hasProperty($property))
		{
			return null;
		}

		$reflectionProperty = $reflectionClass->getProperty($property);
		$reflectionProperty->setAccessible(true);

		return $reflectionProperty->getValue($object);
	}

	/**
	 * Sets the value in an object
	 *
	 * @param   object  $object    The object
	 * @param   string  $property  The property
	 * @param   mixed   $value     The value
	 *
	 * @return  void
	 */
	protected function setValue($object, $property, $value)
	{
		$reflectionClass = new ReflectionClass($object);

		if (!$reflectionClass->hasProperty($property))
		{
			$object->{$property} = $value;

			return;
		}

		$reflectionProperty = $reflectionClass->getProperty($property);
		$reflectionProperty->setAccessible(true);

		$reflectionProperty->setValue($object, $value);
	}
}
