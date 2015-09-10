<?php
/**
 * Part of the Joomla Framework Event Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Event;

use Closure;

/**
 * Implementation of a DispatcherInterface supporting prioritized listeners.
 *
 * @since  1.0
 */
class Dispatcher implements DispatcherInterface, CachingDispatcherInterface
{
	/**
	 * An array of registered events indexed by the event names.
	 *
	 * @var    EventInterface[]
	 * @since  1.0
	 */
	protected $events = array();

	/**
	 * An array of cached events indexed by name and cache identifier
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $eventsCache = array();

	/**
	 * An array of ListenersPriorityQueue indexed by the event names.
	 *
	 * @var    ListenersPriorityQueue[]
	 * @since  1.0
	 */
	protected $listeners = array();

	/**
	 * Set an event to the dispatcher. It will replace any event with the same name.
	 *
	 * @param   EventInterface  $event  The event.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function setEvent(EventInterface $event)
	{
		$this->events[$event->getName()] = $event;

		return $this;
	}

	/**
	 * Add an event to this dispatcher, only if it is not existing.
	 *
	 * @param   EventInterface  $event  The event.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function addEvent(EventInterface $event)
	{
		if (!isset($this->events[$event->getName()]))
		{
			$this->events[$event->getName()] = $event;
		}

		return $this;
	}

	/**
	 * Tell if the given event has been added to this dispatcher.
	 *
	 * @param   EventInterface|string  $event  The event object or name.
	 *
	 * @return  boolean  True if the listener has the given event, false otherwise.
	 *
	 * @since   1.0
	 */
	public function hasEvent($event)
	{
		if ($event instanceof EventInterface)
		{
			$event = $event->getName();
		}

		return isset($this->events[$event]);
	}

	/**
	 * Get the event object identified by the given name.
	 *
	 * @param   string  $name     The event name.
	 * @param   mixed   $default  The default value if the event was not registered.
	 *
	 * @return  EventInterface|mixed  The event of the default value.
	 *
	 * @since   1.0
	 */
	public function getEvent($name, $default = null)
	{
		if (isset($this->events[$name]))
		{
			return $this->events[$name];
		}

		return $default;
	}

	/**
	 * Remove an event from this dispatcher. The registered listeners will remain.
	 *
	 * @param   EventInterface|string  $event  The event object or name.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function removeEvent($event)
	{
		if ($event instanceof EventInterface)
		{
			$event = $event->getName();
		}

		if (isset($this->events[$event]))
		{
			unset($this->events[$event]);
		}

		return $this;
	}

	/**
	 * Get the registered events.
	 *
	 * @return  EventInterface[]  The registered event.
	 *
	 * @since   1.0
	 */
	public function getEvents()
	{
		return $this->events;
	}

	/**
	 * Clear all events.
	 *
	 * @return  EventInterface[]  The old events.
	 *
	 * @since   1.0
	 */
	public function clearEvents()
	{
		$events = $this->events;
		$this->events = array();

		return $events;
	}

	/**
	 * Count the number of registered event.
	 *
	 * @return  integer  The numer of registered events.
	 *
	 * @since   1.0
	 */
	public function countEvents()
	{
		return count($this->events);
	}

	/**
	 * Attaches a listener to an event
	 *
	 * @param   string    $eventName  The event to listen to.
	 * @param   callable  $callback   A callable function
	 * @param   integer   $priority   The priority at which the $callback executed
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function addListener($eventName, callable $callback, $priority = 0)
	{
		if (!isset($this->listeners[$eventName]))
		{
			$this->listeners[$eventName] = new ListenersPriorityQueue;
		}

		$this->listeners[$eventName]->add($callback, $priority);

		return $this;
	}

	/**
	 * Get the priority of the given listener for the given event.
	 *
	 * @param   string    $eventName  The event to listen to.
	 * @param   callable  $callback   A callable function
	 *
	 * @return  mixed  The listener priority or null if the listener doesn't exist.
	 *
	 * @since   1.0
	 */
	public function getListenerPriority($eventName, callable $callback)
	{
		if (isset($this->listeners[$eventName]))
		{
			return $this->listeners[$eventName]->getPriority($callback);
		}

		return null;
	}

	/**
	 * Get the listeners registered to the given event.
	 *
	 * @param   string  $event  The event to fetch listeners for
	 *
	 * @return  object[]  An array of registered listeners sorted according to their priorities.
	 *
	 * @since   1.0
	 */
	public function getListeners($event)
	{
		if (isset($this->listeners[$event]))
		{
			return $this->listeners[$event]->getAll();
		}

		return array();
	}

	/**
	 * Tell if the given listener has been added.
	 *
	 * If an event is specified, it will tell if the listener is registered for that event.
	 *
	 * @param   callable  $callback   The callable to check is listening to the event.
	 * @param   string    $eventName  The event to check a listener is subscribed to.
	 *
	 * @return  boolean  True if the listener is registered, false otherwise.
	 *
	 * @since   1.0
	 */
	public function hasListener(callable $callback, $eventName = null)
	{
		if ($eventName)
		{
			if (isset($this->listeners[$eventName]))
			{
				return $this->listeners[$eventName]->has($callback);
			}
		}
		else
		{
			foreach ($this->listeners as $queue)
			{
				if ($queue->has($callback))
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Removes an event listener from the specified event.
	 *
	 * @param   string    $eventName  The event to remove a listener from.
	 * @param   callable  $callback   The listener to remove.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function removeListener($eventName, callable $listener)
	{
		if (isset($this->listeners[$eventName]))
		{
			$this->listeners[$eventName]->remove($listener);
		}
	}

	/**
	 * Clear the listeners in this dispatcher.
	 *
	 * If an event is specified, the listeners will be cleared only for that event.
	 *
	 * @param   string  $event  The event name.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function clearListeners($event = null)
	{
		if ($event)
		{
			if (isset($this->listeners[$event]))
			{
				unset($this->listeners[$event]);
			}
		}
		else
		{
			$this->listeners = array();
		}

		return $this;
	}

	/**
	 * Count the number of registered listeners for the given event.
	 *
	 * @param   string  $event  The event name.
	 *
	 * @return  integer  The number of registered listeners for the given event.
	 *
	 * @since   1.0
	 */
	public function countListeners($event)
	{
		return isset($this->listeners[$event]) ? count($this->listeners[$event]) : 0;
	}

	/**
	 * Dispatches an event to all registered listeners.
	 *
	 * @param   string          $name   The name of the event to dispatch.
	 * @param   EventInterface  $event  The event to pass to the event handlers/listeners.
	 *
	 * @return  EventInterface
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function dispatch($name, EventInterface $event = null)
	{
		// If no event has been supplied use the default event for the called event name
		if (!($event instanceof EventInterface))
		{
			$event = $this->getDefaultEvent($name);
		}

		// Get the cache identified for cacheable events.
		$identifier = null;

		if ($event instanceof CacheableEvent)
		{
			$identifier = $event->getCacheIdentifier();
		}

		// If the event is cacheable try to find a cached result to return
		if (($event instanceof CacheableEvent) && isset($this->eventsCache[$name]))
		{
			if (isset($this->eventsCache[$name][$identifier]))
			{
				return $this->eventsCache[$name][$identifier];
			}
		}

		// Dispatch the event through all the listeners registered with this dispatcher
		if (isset($this->listeners[$event->getName()]))
		{
			$clonedQueue = clone $this->listeners[$event->getName()];

			foreach ($clonedQueue as $listener)
			{
				if ($event->isStopped())
				{
					return $event;
				}

				call_user_func($listener, $event);
			}
		}

		// If the event is cacheable and we're here we need to cache it for future use
		if (($event instanceof CacheableEvent))
		{
			if (!isset($this->eventsCache[$name]))
			{
				$this->eventsCache[$name] = array();
			}

			$this->eventsCache[$name][$identifier] = $event;
		}

		return $event;
	}

	/**
	 * Trigger an event.
	 *
	 * @param   EventInterface|string  $event  The event object or name.
	 *
	 * @return  EventInterface  The event after being passed through all listeners.
	 *
	 * @since   1.0
	 * @deprecated  3.0  Use dispatch() instead.
	 */
	public function triggerEvent($event)
	{
		if (!($event instanceof EventInterface))
		{
			$event = $this->getDefaultEvent($event);
		}

		return $this->dispatch($event->getName(), $event);
	}

	/**
	 * Clears the dispatcher's cache for a specific event name. If no event name is supplied the entire
	 * cache is cleared. If the event name supplied does not exist in the cache no operation is performed.
	 *
	 * @param   string|null  $name  The event name to uncache
	 *
	 * @return  void
	 */
	public function uncache($name = null)
	{
		// When no name is specified we uncache all events
		if (empty($name))
		{
			$this->eventsCache = array();

			return;
		}

		// When an event name is specified we uncache this event only
		if (isset($this->eventsCache[$name]))
		{
			unset($this->eventsCache[$name]);
		}
	}

	/**
	 * Get an event object for the specified event name
	 *
	 * @param   string  $name  The event name to get an EventInterface object for
	 *
	 * @return  EventInterface
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	private function getDefaultEvent($name)
	{
		if (isset($this->events[$name]))
		{
			return $this->events[$name];
		}

		return new Event($name);
	}
}
