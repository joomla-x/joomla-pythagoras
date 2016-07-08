<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM;

use Joomla\ORM\Exception\OrmException;
use ReflectionClass;
use ReflectionException;

/**
 * Defines the Id accessor registry
 */
class IdAccessorRegistry
{
	/** @var callable[] The mapping of class names to their getter and setter functions */
	protected $idAccessorFunctions = [];

	/**
	 * Gets the Id of an entity
	 *
	 * @param object $entity The entity whose Id we want
	 *
	 * @return mixed The Id of the entity
	 * @throws OrmException Thrown if no Id getter is registered for the entity
	 */
	public function getEntityId($entity)
	{
		$className = get_class($entity);

		if (!isset($this->idAccessorFunctions[$className]["getter"]))
		{
			if (!$this->hasId($entity))
			{
				throw new OrmException("No Id getter registered for class $className");
			}

			$this->registerReflectionIdAccessors($className, 'id');
		}

		try
		{
			return call_user_func($this->idAccessorFunctions[$className]["getter"], $entity);
		}
		catch (ReflectionException $e)
		{
			throw new OrmException("Failed to get entity Id", 0, $e);
		}
	}

	/**
	 * Sets the entity Id
	 *
	 * @param object $entity The entity whose Id we're setting
	 * @param mixed  $id     The Id to set
	 *
	 * @throws OrmException Thrown if no Id setter has been registered for this entity
	 */
	public function setEntityId($entity, $id)
	{
		$className = get_class($entity);

		if (!isset($this->idAccessorFunctions[$className]["setter"]))
		{
			if (!$this->hasId($entity))
			{
				throw new OrmException("No Id setter registered for class $className");
			}

			$this->registerReflectionIdAccessors($className, 'id');
		}

		try
		{
			call_user_func($this->idAccessorFunctions[$className]["setter"], $entity, $id);
		}
		catch (ReflectionException $e)
		{
			throw new OrmException("Failed to set entity Id", 0, $e);
		}
	}

	/**
	 * Registers functions that get an Id and set the Id for all instances of the input class name
	 *
	 * @param string|array $classNames The name or list of names of classes whose Id getter functions we're registering
	 * @param callable     $getter     The function that accepts an entity as a parameter and returns its Id
	 * @param callable     $setter     The function that accepts an entity and new Id as parameters and sets the Id
	 */
	public function registerIdAccessors($classNames, callable $getter, callable $setter = null)
	{
		foreach ((array) $classNames as $className)
		{
			$this->idAccessorFunctions[$className] = [
				"getter" => $getter,
				"setter" => $setter
			];
		}
	}

	/**
	 * Registers accessors that use reflection to set Id properties in the input classes
	 *
	 * @param string|array $classNames     The name or list of names of classes whose Id accessors we're registering
	 * @param string       $idPropertyName The name of the Id property we're registering
	 */
	public function registerReflectionIdAccessors($classNames, $idPropertyName)
	{
		foreach ((array) $classNames as $className)
		{
			$getter = function ($entity) use ($className, $idPropertyName)
			{
				$reflectionClass = new ReflectionClass($className);
				$property        = $reflectionClass->getProperty($idPropertyName);
				$property->setAccessible(true);

				return $property->getValue($entity);
			};
			$setter = function ($entity, $id) use ($className, $idPropertyName)
			{
				$reflectionClass = new ReflectionClass($className);
				$property        = $reflectionClass->getProperty($idPropertyName);
				$property->setAccessible(true);
				$property->setValue($entity, $id);
			};
			$this->registerIdAccessors($classNames, $getter, $setter);
		}
	}

	/**
	 * @param $entity
	 *
	 * @return bool
	 */
	private function hasId($entity)
	{
		return (new ReflectionClass($entity))->hasProperty('id');
	}
}
