<?php
/**
 * Part of the Joomla Framework Event Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Event;

/**
 * Implementation of a DispatcherInterface which is doing nothing.
 *
 * @since  1.0
 */
class NullDispatcher implements DispatcherInterface
{
	/**
	 * Attaches a listener to an event
	 *
	 * @param   string   $eventName The event to listen to.
	 * @param   callable $callback  A callable function.
	 * @param   integer  $priority  The priority at which the $callback executed.
	 *
	 * @return  boolean
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function addListener($eventName, callable $callback, $priority = 0)
	{
		return true;
	}

	/**
	 * Dispatches an event to all registered listeners.
	 *
	 * @param   EventInterface $event The event to pass to the event handlers/listeners.
	 *
	 * @return  EventInterface
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function dispatch(EventInterface $event)
	{
		return $event;
	}

	/**
	 * Removes an event listener from the specified event.
	 *
	 * @param   string   $eventName The event to remove a listener from.
	 * @param   callable $listener  The listener to remove.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function removeListener($eventName, callable $listener)
	{
		return;
	}
}
