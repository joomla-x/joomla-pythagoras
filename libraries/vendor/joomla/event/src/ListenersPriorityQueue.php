<?php
/**
 * Part of the Joomla Framework Event Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Event;

/**
 * A class containing an inner listeners priority queue that can be iterated multiple times.
 * One instance of ListenersPriorityQueue is used per Event in the Dispatcher.
 *
 * @since  1.0
 */
class ListenersPriorityQueue extends \SplPriorityQueue
{
	/**
	 * A decreasing counter used to compute the internal priority as an array because SplPriorityQueue dequeues elements with the same priority.
	 *
	 * @var    integer
	 * @since  1.0
	 */
	private $counter = PHP_INT_MAX;

	/**
	 * Add a listener with the given priority only if not already present.
	 *
	 * @param   callable  $callback   A callable function acting as an event listener.
	 * @param   integer   $priority   The listener priority.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function add(callable $callback, $priority)
	{
		if (!$this->has($callback))
		{
			// Compute the internal priority as an array.
			$priority = array($priority, $this->counter--);

			$this->insert($callback, $priority);
		}

		return $this;
	}

	/**
	 * Remove a listener from the queue.
	 *
	 * @param   callable  $callback  A callable function acting as an event listener.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function remove(callable $callback)
	{
		if ($this->has($callback))
		{
			// Clone ourselves to retain the existing queue data
			$self = clone $this;
			$self->setExtractFlags(self::EXTR_BOTH);

			// And now clear our queue
			$this->extract();

			foreach ($self as $listener)
			{
				if ($listener['data'] !== $callback)
				{
					$this->insert($listener['data'], $listener['priority']);
				}
			}
		}

		return $this;
	}

	/**
	 * Tell if the listener exists in the queue.
	 *
	 * @param   callable  $callback  A callable function acting as an event listener.
	 *
	 * @return  boolean  True if it exists, false otherwise.
	 *
	 * @since   1.0
	 */
	public function has(callable $callback)
	{
		$self = clone $this;

		foreach ($self as $item)
		{
			if ($item === $callback)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the priority of the given listener.
	 *
	 * @param   callable  $callback  A callable function acting as an event listener.
	 * @param   mixed     $default   The default value to return if the listener doesn't exist.
	 *
	 * @return  mixed  The listener priority if it exists or the specified default value
	 *
	 * @since   1.0
	 */
	public function getPriority(callable $callback, $default = null)
	{
		$self = clone $this;
		$self->setExtractFlags(self::EXTR_BOTH);

		foreach ($self as $item)
		{
			if ($item['data'] === $callback)
			{
				return $item['priority'][0];
			}
		}

		return $default;
	}

	/**
	 * Get all listeners contained in this queue, sorted according to their priority.
	 *
	 * @return  object[]  An array of listeners.
	 *
	 * @since   1.0
	 */
	public function getAll()
	{
		$listeners = array();

		// Get a clone of the queue.
		$queue = $this->getIterator();

		foreach ($queue as $listener)
		{
			$listeners[] = $listener;
		}

		return $listeners;
	}

	/**
	 * Get the priority queue with its cursor on top of the heap.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function getIterator()
	{
		// SplPriorityQueue queue is a heap.
		$queue = clone $this;

		if (!$queue->isEmpty())
		{
			$queue->top();
		}

		return $queue;
	}
}
