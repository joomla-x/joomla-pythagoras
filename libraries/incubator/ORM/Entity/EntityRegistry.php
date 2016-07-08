<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Entity;

use Joomla\ORM\ChangeTracker\ChangeTracker;
use Joomla\ORM\Exception\OrmException;
use Joomla\ORM\Id\IdAccessorRegistry;

/**
 * Defines an entity registry
 */
class EntityRegistry
{
	/** @var IdAccessorRegistry The Id accessory registry */
	protected $idAccessorRegistry = null;

	/** @var ChangeTracker The change tracker */
	protected $changeTracker = null;

	/** @var array The mapping of entities' object hash Ids to their various states */
	private $entityStates = [];

	/** @var array The mapping of class names to a list of entities of that class */
	private $entities = [];

	/**
	 * Maps aggregate root children to their roots as well as functions that can set the child's aggregate root Id
	 * Each entry is an array of arrays with the following keys:
	 *      "aggregateRoot" => The aggregate root
	 *      "child" => The entity whose aggregate root Id will be set to the Id of the aggregate root
	 *      "function" => The function to execute that actually sets the aggregate root Id in the child
	 *          Note:  The function MUST have two parameters: first for the aggregate root and a second for the child
	 *
	 * @var array
	 */
	private $aggregateRootChildren = [];

	/**
	 * @param IdAccessorRegistry $idAccessorRegistry The Id accessor registry
	 * @param ChangeTracker      $changeTracker      The change tracker
	 */
	public function __construct(IdAccessorRegistry $idAccessorRegistry, ChangeTracker $changeTracker)
	{
		$this->idAccessorRegistry = $idAccessorRegistry;
		$this->changeTracker      = $changeTracker;
	}

	/**
	 * Clears all the contents of the registry
	 * This should only be called through a unit of work
	 */
	public function clear()
	{
		$this->changeTracker->stopTrackingAll();
		$this->entities     = [];
		$this->entityStates = [];
		$this->clearAggregateRoots();
	}

	/**
	 * Clears all aggregate root child functions
	 */
	public function clearAggregateRoots()
	{
		$this->aggregateRootChildren = [];
	}

	/**
	 * Deregisters an entity
	 * This should only be called through a unit of work
	 *
	 * @param object $entity The entity to detach
	 */
	public function deregisterEntity($entity)
	{
		$entityState = $this->getEntityState($entity);
		unset($this->aggregateRootChildren[$this->getObjectHashId($entity)]);

		if ($entityState == EntityStates::QUEUED || $entityState == EntityStates::REGISTERED)
		{
			$className                         = $this->getClassName($entity);
			$objectHashId                      = $this->getObjectHashId($entity);
			$entityId                          = $this->idAccessorRegistry->getEntityId($entity);
			$this->entityStates[$objectHashId] = EntityStates::UNREGISTERED;
			unset($this->entities[$className][$entityId]);
			$this->changeTracker->stopTracking($entity);
		}
	}

	/**
	 * Gets the object's class name
	 *
	 * @param mixed $object The object whose class name we want
	 *
	 * @return string The object's class name
	 */
	public function getClassName($object)
	{
		return get_class($object);
	}

	/**
	 * Gets the list of all registered entities
	 *
	 * @return object[] The list of all registered entities
	 */
	public function getEntities()
	{
		if (count($this->entities) == 0)
		{
			return [];
		}

		// Flatten the  list of entities
		$entities = [];
		array_walk_recursive($this->entities, function ($entity) use (&$entities)
		{
			$entities[] = $entity;
		});

		return $entities;
	}

	/**
	 * Attempts to get a registered entity
	 *
	 * @param string     $className The name of the class the entity belongs to
	 * @param int|string $id        The entity's Id
	 *
	 * @return object|null The entity if it was found, otherwise null
	 */
	public function getEntity($className, $id)
	{
		if (!isset($this->entities[$className]) || !isset($this->entities[$className][$id]))
		{
			return null;
		}

		return $this->entities[$className][$id];
	}

	/**
	 * Gets the entity state for the input entity
	 *
	 * @param object $entity The entity to check
	 *
	 * @return int The entity state
	 */
	public function getEntityState($entity)
	{
		$objectHashId = $this->getObjectHashId($entity);

		if (!isset($this->entityStates[$objectHashId]))
		{
			return EntityStates::NEVER_REGISTERED;
		}

		return $this->entityStates[$objectHashId];
	}

	/**
	 * Gets a unique hash Id for an object
	 *
	 * @param mixed $object The object whose hash we want
	 *
	 * @return string The object hash Id
	 */
	public function getObjectHashId($object)
	{
		return spl_object_hash($object);
	}

	/**
	 * Gets whether or not an entity is registered
	 *
	 * @param object $entity The entity to check
	 *
	 * @return bool True if the entity is registered, otherwise false
	 */
	public function isRegistered($entity)
	{
		try
		{
			$entityId = $this->idAccessorRegistry->getEntityId($entity);

			return $this->getEntityState($entity) == EntityStates::REGISTERED
			       || isset($this->entities[$this->getClassName($entity)][$entityId]);
		}
		catch (OrmException $e)
		{
			return false;
		}
	}

	/**
	 * Registers a function to set the aggregate root Id in a child entity after the aggregate root has been inserted
	 * Since the child depends on the aggregate root's Id being set, make sure the root is inserted before the child
	 *
	 * @param object   $aggregateRoot The aggregate root
	 * @param object   $child         The child of the aggregate root
	 * @param callable $function      The function that contains the logic to set the aggregate root Id in the child
	 */
	public function registerAggregateRootCallback($aggregateRoot, $child, callable $function)
	{
		$childObjectHashId = $this->getObjectHashId($child);

		if (!isset($this->aggregateRootChildren[$childObjectHashId]))
		{
			$this->aggregateRootChildren[$childObjectHashId] = [];
		}

		$this->aggregateRootChildren[$childObjectHashId][] = [
			"aggregateRoot" => $aggregateRoot,
			"child"         => $child,
			"function"      => $function
		];
	}

	/**
	 * Registers an entity
	 *
	 * @param object $entity The entity to register
	 *
	 * @throws OrmException Thrown if there was an error registering the entity
	 */
	public function registerEntity(&$entity)
	{
		$className    = $this->getClassName($entity);
		$objectHashId = $this->getObjectHashId($entity);
		$entityId     = $this->idAccessorRegistry->getEntityId($entity);

		if (!isset($this->entities[$className]))
		{
			$this->entities[$className] = [];
		}

		if (isset($this->entities[$className][$entityId]))
		{
			// Change the reference of the input entity to the one that's already registered
			$entity = $this->getEntity($this->getClassName($entity), $entityId);
		}
		else
		{
			// Register this entity
			$this->changeTracker->startTracking($entity);
			$this->entities[$className][$entityId] = $entity;
			$this->entityStates[$objectHashId]     = EntityStates::REGISTERED;
		}
	}

	/**
	 * Runs any aggregate root child functions registered for the entity
	 *
	 * @param object $child The child whose aggregate root functions we're running
	 */
	public function runAggregateRootCallbacks($child)
	{
		$objectHashId = $this->getObjectHashId($child);

		if (isset($this->aggregateRootChildren[$objectHashId]))
		{
			foreach ($this->aggregateRootChildren[$objectHashId] as $aggregateRootData)
			{
				$aggregateRoot = $aggregateRootData["aggregateRoot"];
				$aggregateRootData["function"]($aggregateRoot, $child);
			}
		}
	}

	/**
	 * Sets an entity's state
	 *
	 * @param object $entity      The entity whose state we're setting
	 * @param int    $entityState The entity state
	 */
	public function setState($entity, $entityState)
	{
		$this->entityStates[$this->getObjectHashId($entity)] = $entityState;
	}
}
