<?php
/**
 * Part of the Joomla Framework Extension Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Extension;

use Joomla\Service\Query;

/**
 * Class Extension
 *
 * @package Joomla\Extension
 *
 * @since   1.0
 */
class Extension implements ExtensionInterface
{
	/** @var callable[][] */
	private $listeners = [];

	/** @var callable[][] */
	private $queryHandlers = [];

	/** @var string[] */
	private $contentTypes = [];

	/**
	 * Get the listeners
	 *
	 * @param   string $eventName The name of the event
	 *
	 * @return  callable[]
	 */
	public function getListeners($eventName)
	{
		if (!key_exists($eventName, $this->listeners))
		{
			return [];
		}

		return $this->listeners[$eventName];
	}

	/**
	 * Add a listener
	 *
	 * @param   string   $eventName The name of the event
	 * @param   callable $listener  The event handler
	 *
	 * @return  void
	 */
	public function addListener($eventName, $listener)
	{
		if (!key_exists($eventName, $this->listeners))
		{
			$this->listeners[$eventName] = [];
		}

		$this->listeners[$eventName][] = $listener;
	}

	/**
	 * Get the listeners
	 *
	 * @param   Query $query The query
	 *
	 * @return  callable[]
	 */
	public function getQueryHandlers(Query $query)
	{
		if (!key_exists(get_class($query), $this->queryHandlers))
		{
			return [];
		}

		return $this->queryHandlers[get_class($query)];
	}

	/**
	 * Add a query handler
	 *
	 * @param   string   $className The name of the query class
	 * @param   callable $handler   The event handler
	 *
	 * @return  void
	 */
	public function addQueryHandler($className, $handler)
	{
		$className = ltrim($className, '\\');

		if (!key_exists($className, $this->queryHandlers))
		{
			$this->queryHandlers[$className] = [];
		}

		$this->queryHandlers[$className][] = $handler;
	}

	/**
	 * @return string[]
	 */
	public function getContentTypes()
	{
		return $this->contentTypes;
	}

	/**
	 * Add a content type
	 *
	 * @param   string   $name      The name of the content type
	 * @param   string   $className The name of the query class
	 *
	 * @return  void
	 */
	public function addContentType($name, $className)
	{
		$this->contentTypes[$name] = $className;
	}
}
