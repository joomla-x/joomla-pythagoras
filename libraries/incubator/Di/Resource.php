<?php
/**
 * Part of the Joomla Framework DI Package
 *
 * @copyright  Copyright (C) 2013 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Di;

/**
 * Defines the representation of a resource.
 *
 * @package  Joomla/di
 *
 * @since    2.0
 */
class Resource
{
	const NO_SHARE = 0;
	const SHARE = 1;
	const NO_PROTECT = 0;
	const PROTECT = 2;

	/** @var Container */
	private $container;

	/** @var  mixed */
	private $instance = null;

	/** @var \Callable */
	private $factory = null;

	/** @var bool */
	private $shared = false;

	/** @var bool */
	private $protected = false;

	/**
	 * Create a resource representation
	 *
	 * @param   Container $container The container
	 * @param   mixed     $value     The resource or its factory closure
	 * @param   int       $mode      Resource mode, defaults to Resource::NO_SHARE | Resource::NO_PROTECT
	 */
	public function __construct(Container $container, $value, $mode = 0)
	{
		$this->container = $container;
		$this->shared    = ($mode & self::SHARE) == self::SHARE;
		$this->protected = ($mode & self::PROTECT) == self::PROTECT;

		if (is_callable($value))
		{
			$this->factory = $value;
		}
		else
		{
			if ($this->shared)
			{
				$this->instance = $value;
			}

			if (is_object($value))
			{
				$this->factory = function () use ($value) {

					return clone $value;
				};
			}
			else
			{
				$this->factory = function () use ($value) {

					return $value;
				};
			}
		}
	}

	/**
	 * Get an instance of the resource
	 *
	 * If a factory was provided, the resource is created and - if it is a shared resource - cached internally.
	 * If the resource was provided directly, that resource is returned.
	 *
	 * @return mixed
	 */
	public function getInstance()
	{
		$callable = $this->factory;

		if ($this->isShared())
		{
			if ($this->instance === null)
			{
				$this->instance = call_user_func($callable, $this->container);
			}

			return $this->instance;
		}

		return call_user_func($callable, $this->container);
	}

	/**
	 * Check whether the resource is shared
	 *
	 * @return boolean
	 */
	public function isShared()
	{
		return $this->shared;
	}

	/**
	 * Get the factory
	 *
	 * @return  callable
	 */
	public function getFactory()
	{
		return $this->factory;
	}

	/**
	 * Reset the resource
	 *
	 * The instance cache is cleared, so that the next call to get() returns a new instance.
	 * This has an effect on shared, non-protected resources only.
	 *
	 * @return  boolean true, if the resource was reset, false otherwise
	 */
	public function reset()
	{
		if ($this->isShared() && !$this->isProtected())
		{
			$this->instance = null;

			return true;
		}

		return false;
	}

	/**
	 * Check whether the resource is protected
	 *
	 * @return boolean
	 */
	public function isProtected()
	{
		return $this->protected;
	}
}
