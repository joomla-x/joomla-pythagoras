<?php
/**
 * @package     Joomla.Framework
 * @subpackage  Service Layer
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Service;

/**
 * Immutable trait.
 * 
 * Implemented as an abstract class here because traits are PHP 5.4 minimum.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class Immutable
{
	// Flag indicating object construction completed.
	private $constructed = false;

	// Array of command arguments.
	private $args = array();

	/**
	 * Constructor.
	 * 
	 * @since   __DEPLOY_VERSION__
	 */
	public function __construct()
	{
		if ($this->constructed)
		{
			throw new \RuntimeException('Cannot call constructor more than once, object is immutable.');
		}

		// Save the name of the class as a property.
		$reflectionClass = new \ReflectionClass($this);
		$this->name = $reflectionClass->getShortName();

		// Flag object construction completed.
		$this->constructed = true;
	}

	/**
	 * Magic call method.
	 * 
	 * Method names starting with "get" are treated as property getters.
	 * All other (non-existant) methods will throw an exception.
	 * 
	 * @param   string  $name  Name of the method being called.
	 * @param   array   $args  Array of arguments passed to the method.
	 * 
	 * @return  mixed
	 * 
	 * @throws  \InvalidArgumentException
	 * @since   __DEPLOY_VERSION__
	 */
	public function __call($name, array $args)
	{
		// If the method call is not a getter, throw an exception.
		if (strtolower(substr($name, 0, 3)) != 'get')
		{
			throw new \InvalidArgumentException('Method does not exist: ' . $name);
		}

		// Get name of the property being requested.
		$key = strtolower(substr($name, 3));

		// If property does not exist throw an exception.
		if (!isset($this->args[$key]))
		{
			throw new \InvalidArgumentException('Cannot get property because property does not exist: ' . $key);
		}

		return $this->args[$key];
	}

	/**
	 * Magic getter.
	 * 
	 * If the property exists, it will return its value;
	 * otherwise it will throw an exception.
	 * 
	 * @param   string  $key  Property name (case-insensitive).
	 * 
	 * @return  mixed
	 * 
	 * @throws  \InvalidArgumentException
	 * @since   __DEPLOY_VERSION__
	 */
	public function __get($key)
	{
		if (!isset($this->args[strtolower($key)]))
		{
			throw new \InvalidArgumentException('Cannot read property because property does not exist: ' . $key);
		}

		return $this->args[strtolower($key)];
	}

	/**
	 * Magic setter.
	 * 
	 * Since the object is immutable, this always throws an exception
	 * once object creation has been completed.
	 * 
	 * @param   string  $key    Property name (case-insensitive).
	 * @param   mixed   $value  Property value.
	 * 
	 * @return  void
	 * 
	 * @throws  \InvalidArgumentException
	 * @since   __DEPLOY_VERSION__
	 */
	public function __set($key, $value)
	{
		if ($this->constructed)
		{
			throw new \InvalidArgumentException('Cannot set property, object is immutable.');
		}

		// Save key/value pair in argument array.
		$this->args[strtolower($key)] = $value;
	}

	/**
	 * Get the properties of the object.
	 * 
	 * @return  array of key-value pairs.
	 * 
	 * @since  __DEPLOY_VERSION__
	 */
	public function getProperties()
	{
		return $this->args;
	}
}
