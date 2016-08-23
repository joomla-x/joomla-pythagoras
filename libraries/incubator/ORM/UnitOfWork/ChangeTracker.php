<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\UnitOfWork;

use Joomla\ORM\Exception\OrmException;
use ReflectionClass;

/**
 * Defines the change tracker
 *
 * @package  Joomla/ORM
 *
 * @since    __DEPLOY_VERSION__
 */
class ChangeTracker
{
	/** @var object[] The mapping of object Ids to their original data */
	protected $originalData = [];

	/**
	 * The mapping of class names to comparison functions
	 * Each function should return true if the entities are the same, otherwise false
	 *
	 * @var callable[]
	 */
	protected $comparators = [];

	/**
	 * Gets whether or not an entity has changed since it was registered
	 *
	 * @param   object  $entity  The entity to check
	 *
	 * @return  boolean  True if the entity has changed, otherwise false
	 * @throws  OrmException  if the entity was not registered in the first place
	 */
	public function hasChanged($entity)
	{
		$hashId = $this->getHashId($entity);

		if (!isset($this->originalData[$hashId]))
		{
			throw new OrmException("Entity is not registered");
		}

		if (isset($this->comparators[get_class($entity)]))
		{
			return $this->hasChangedUsingComparisonFunction($entity);
		}

		return $this->hasChangedUsingReflection($entity);
	}

	/**
	 * Registers a function that compares two entities and determines whether or not they're the same
	 *
	 * @param   string    $className   The name of the class whose comparator we're registering
	 * @param   callable  $comparator  The function that accepts two entities and returns whether or not they're the same
	 *
	 * @return  void
	 */
	public function registerComparator($className, callable $comparator)
	{
		$this->comparators[$className] = $comparator;
	}

	/**
	 * Starts tracking an entity
	 *
	 * @param   object  $entity  The entity to start tracking
	 *
	 * @return  void
	 */
	public function startTracking($entity)
	{
		$hashId                      = $this->getHashId($entity);
		$this->originalData[$hashId] = clone $entity;
	}

	/**
	 * Stops tracking an entity
	 *
	 * @param   object  $entity  The entity to deregister
	 *
	 * @return  void
	 */
	public function stopTracking($entity)
	{
		$hashId = $this->getHashId($entity);
		unset($this->originalData[$hashId]);
	}

	/**
	 * Stops tracking all entities
	 *
	 * @return  void
	 */
	public function stopTrackingAll()
	{
		$this->originalData = [];
	}

	/**
	 * Checks to see if an entity has changed using a comparison function
	 *
	 * @param   object  $entity  The entity to check for changes
	 *
	 * @return  boolean  True if the entity has changed, otherwise false
	 */
	protected function hasChangedUsingComparisonFunction($entity)
	{
		$hashId       = $this->getHashId($entity);
		$originalData = $this->originalData[$hashId];

		return !$this->comparators[get_class($entity)]($originalData, $entity);
	}

	/**
	 * Checks to see if an entity has changed using reflection
	 *
	 * @param   object  $entity  The entity to check for changes
	 *
	 * @return  boolean  True if the entity has changed, otherwise false
	 */
	protected function hasChangedUsingReflection($entity)
	{
		// Get all the properties in the original entity and the current one
		$hashId                  = $this->getHashId($entity);
		$currentEntityReflection = new ReflectionClass($entity);
		$currentProperties       = $currentEntityReflection->getProperties();
		$currentPropertiesAsHash = [];

		$originalData             = $this->originalData[$hashId];
		$originalEntityReflection = new ReflectionClass($originalData);
		$originalProperties       = $originalEntityReflection->getProperties();
		$originalPropertiesAsHash = [];

		// Map each property name to its value for the current entity
		foreach ($currentProperties as $currentProperty)
		{
			$currentProperty->setAccessible(true);
			$currentPropertiesAsHash[$currentProperty->getName()] = $currentProperty->getValue($entity);
		}

		// Map each property name to its value for the original entity
		foreach ($originalProperties as $originalProperty)
		{
			$originalProperty->setAccessible(true);
			$originalPropertiesAsHash[$originalProperty->getName()] = $originalProperty->getValue($originalData);
		}

		if (count($originalProperties) != count($currentProperties))
		{
			// Clearly there's a difference here, so update
			return true;
		}

		// Compare all the property values to see if they are identical
		foreach ($originalPropertiesAsHash as $name => $value)
		{
			if (!array_key_exists($name, $currentPropertiesAsHash) || $currentPropertiesAsHash[$name] !== $value)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Gets the hash id of an object
	 *
	 * @param   object  $entity  The entity
	 *
	 * @return  string  The hash id
	 */
	private function getHashId($entity)
	{
		return spl_object_hash($entity);
	}

	/**
	 * Gets the original version of an entity for change detection
	 *
	 * @param   object  $entity  The current entity
	 *
	 * @return  object
	 */
	public function getOriginal($entity)
	{
		$hashId = $this->getHashId($entity);
		$originalData = $this->originalData[$hashId];

		return $originalData;
	}
}
