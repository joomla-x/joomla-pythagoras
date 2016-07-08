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
	 * @param object $entity The entity to check
	 *
	 * @return bool True if the entity has changed, otherwise false
	 * @throws OrmException Thrown if the entity was not registered in the first place
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
	 * @param string   $className  The name of the class whose comparator we're registering
	 * @param callable $comparator The function that accepts two entities and returns whether or not they're the same
	 */
	public function registerComparator($className, callable $comparator)
	{
		$this->comparators[$className] = $comparator;
	}

	/**
	 * Starts tracking an entity
	 *
	 * @param object $entity The entity to start tracking
	 */
	public function startTracking($entity)
	{
		$hashId                      = $this->getHashId($entity);
		$this->originalData[$hashId] = clone $entity;
	}

	/**
	 * Stops tracking an entity
	 *
	 * @param object $entity The entity to deregister
	 */
	public function stopTracking($entity)
	{
		$hashId = $this->getHashId($entity);
		unset($this->originalData[$hashId]);
	}

	/**
	 * Stops tracking all entities
	 */
	public function stopTrackingAll()
	{
		$this->originalData = [];
	}

	/**
	 * Checks to see if an entity has changed using a comparison function
	 *
	 * @param object $entity The entity to check for changes
	 *
	 * @return bool True if the entity has changed, otherwise false
	 */
	protected function hasChangedUsingComparisonFunction($entity)
	{
		$objectHashId = spl_object_hash($entity);
		$originalData = $this->originalData[$objectHashId];

		return !$this->comparators[get_class($entity)]($originalData, $entity);
	}

	/**
	 * Checks to see if an entity has changed using reflection
	 *
	 * @param object $entity The entity to check for changes
	 *
	 * @return bool True if the entity has changed, otherwise false
	 */
	protected function hasChangedUsingReflection($entity)
	{
		// Get all the properties in the original entity and the current one
		$objectHashId            = $this->getHashId($entity);
		$currentEntityReflection = new ReflectionClass($entity);
		$currentProperties       = $currentEntityReflection->getProperties();
		$currentPropertiesAsHash = [];

		$originalData             = $this->originalData[$objectHashId];
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
	 * @param $entity
	 *
	 * @return string
	 */
	private function getHashId($entity)
	{
		return spl_object_hash($entity);
	}
}
