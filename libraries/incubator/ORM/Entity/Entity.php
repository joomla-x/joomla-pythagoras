<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Entity;

use Joomla\ORM\Definition\Parser\Field;
use Joomla\ORM\Exception\PropertyNotFoundException;
use Joomla\ORM\Exception\WriteOnImmutableException;
use Joomla\String\Normalise;

/**
 * Class Entity
 *
 * @package  Joomla/ORM
 *
 * @since    __DEPLOY_VERSION__
 */
class Entity implements EntityInterface
{
	/** @var  bool  Flag whether this entity is immutable */
	private $immutable = false;

	/** @var  \Joomla\ORM\Definition\Parser\Entity $definition The definition of the entity */
	private $definition;

	/** @var  Field[]  The fields */
	private $fields;

	/** @var  Callable[]  Relation resolvers */
	private $relationHandlers;

	/** @var  string|array  The name(s) of the id field */
	private $key;

	/**
	 * Get the type of the entity.
	 *
	 * @return  string  The type (name) of the entity
	 */
	public function type()
	{
		return $this->definition->name;
	}

	/**
	 * Get the field for the primary key.
	 *
	 * @return  string  The name of the field
	 */
	public function key()
	{
		if (empty($this->key) && !empty($this->definition->primary))
		{
			$keys = array_values(
				array_filter(
					preg_split('~[\s,]+~', $this->definition->primary)
				)
			);
			$keys = array_map(
				function ($key) {
					return Normalise::toVariable($key);
				},
				$keys
			);

			$this->key = count($keys) > 1 ? $keys : array_shift($keys);
		}

		if (empty($this->key) && $this->has('id'))
		{
			$this->key = 'id';
		}

		return $this->key;
	}

	/**
	 * Bind data to the entity.
	 *
	 * @param   array $data The data for the entity as a list of key-value pairs
	 *
	 * @return  void
	 */
	public function bind(Array $data)
	{
		foreach ($data as $property => $value)
		{
			if ($this->has($property))
			{
				$this->$property = $value;
			}
		}
	}

	/**
	 * Make the entity readonly.
	 *
	 * Making the entity immutable is not reversible.
	 *
	 * @return  void
	 */
	public function lock()
	{
		$this->immutable = true;
	}

	/**
	 * Check if the entity is locked
	 *
	 * @return  boolean
	 */
	public function isImmutable()
	{
		return $this->immutable;
	}

	/**
	 * Check if the entity has a certain property.
	 *
	 * @param   string $property The name of the property
	 *
	 * @return  boolean
	 */
	public function has($property)
	{
		return isset($this->fields[$property]);
	}

	/**
	 * Get a property from the entity.
	 *
	 * @param   string $property The name of the property
	 *
	 * @return  mixed  The value of the property
	 */
	public function __get($property)
	{
		if (isset($this->fields[$property]))
		{
			return $this->fields[$property]->value;
		}

		if (isset($this->relationHandlers[$property]))
		{
			return call_user_func($this->relationHandlers[$property]);
		}

		throw new PropertyNotFoundException("Unknown property {$property}");
	}

	/**
	 * Set a property in the entity.
	 *
	 * @param   string $property The name of the property
	 * @param   mixed  $value    The value for the property
	 *
	 * @return  mixed
	 */
	public function __set($property, $value)
	{
		if ($this->isImmutable())
		{
			throw new WriteOnImmutableException("Write attempt on immutable entity");
		}

		if (!$this->has($property))
		{
			throw new PropertyNotFoundException("Unknown property {$property}");
		}

		$this->fields[$property]->value = $value;
	}

	/**
	 * Get the field values
	 *
	 * @return  array  The field values, indexed by field name
	 */
	public function asArray()
	{
		$fields = [
			'@type' => $this->type(),
			'@key'  => $this->key()
		];

		foreach ($this->fields as $name => $field)
		{
			$fields[$name] = $field->value;
		}

		return $fields;
	}

	/**
	 * Get the original data structure
	 *
	 * @return  \Joomla\ORM\Definition\Parser\Entity
	 */
	public function getDefinition()
	{
		return $this->definition;
	}
}
