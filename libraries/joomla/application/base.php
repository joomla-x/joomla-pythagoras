<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

use Joomla\Application\AbstractApplication;
use Joomla\Event\Dispatcher;
use Joomla\Event\DispatcherInterface;
use Joomla\Event\Event;

/**
 * Joomla Platform Base Application Class
 *
 * @since  12.1
 */
abstract class JApplicationBase extends AbstractApplication
{
	/**
	 * The application dispatcher object.
	 *
	 * @var    DispatcherInterface
	 * @since  12.1
	 */
	protected $dispatcher;

	/**
	 * The application identity object.
	 *
	 * @var    JUser
	 * @since  12.1
	 */
	protected $identity;

	/**
	 * Get the application identity.
	 *
	 * @return  mixed  A JUser object or null.
	 *
	 * @since   12.1
	 */
	public function getIdentity()
	{
		return $this->identity;
	}

	/**
	 * Registers a handler to a particular event group.
	 *
	 * @param   string    $event    The event name.
	 * @param   callable  $handler  The handler, a function or an instance of a event object.
	 *
	 * @return  JApplicationBase  The application to allow chaining.
	 *
	 * @since   12.1
	 */
	public function registerEvent($event, callable $handler)
	{
		if ($this->dispatcher instanceof DispatcherInterface)
		{
			$this->dispatcher->addListener($event, $handler);
		}

		return $this;
	}

	/**
	 * Returns the event dispatcher of the application. This is a temporary method added during the Event package
	 * refactoring.
	 *
	 * @deprecated
	 *
	 * TODO REFACTOR ME! Remove this and go through a Container.
	 *
	 * @return  DispatcherInterface
	 */
	public function getDispatcher()
	{
		if (!($this->dispatcher instanceof DispatcherInterface))
		{
			$this->loadDispatcher();
		}

		return $this->dispatcher;
	}

	/**
	 * Calls all handlers associated with an event group.
	 *
	 * @param   string        $eventName  The event name.
	 * @param   array|Event   $args       An array of arguments or an Event object (optional).
	 *
	 * TODO Force $args to be an Event object
	 *
	 * @return  array   An array of results from each function call, or null if no dispatcher is defined.
	 *
	 * @since   12.1
	 */
	public function triggerEvent($eventName, $args = null)
	{
		// @todo REFACTOR ME! Get the Dispatcher through a Container
		$dispatcher = $this->getDispatcher();

		if ($dispatcher instanceof DispatcherInterface)
		{
			if ($args instanceof Event)
			{
				return $dispatcher->dispatch($eventName, $args);
			}

			$event = new Event($eventName, $args);

			return $dispatcher->dispatch($eventName, $event);
		}

		return null;
	}

	/**
	 * Allows the application to load a custom or default dispatcher.
	 *
	 * The logic and options for creating this object are adequately generic for default cases
	 * but for many applications it will make sense to override this method and create event
	 * dispatchers, if required, based on more specific needs.
	 *
	 * @param   DispatcherInterface  $dispatcher  An optional dispatcher object. If omitted, the factory dispatcher is created.
	 *
	 * @return  JApplicationBase This method is chainable.
	 *
	 * @since   12.1
	 */
	public function loadDispatcher(DispatcherInterface $dispatcher = null)
	{
		$this->dispatcher = ($dispatcher === null) ? new Dispatcher() : $dispatcher;

		return $this;
	}

	/**
	 * Allows the application to load a custom or default identity.
	 *
	 * The logic and options for creating this object are adequately generic for default cases
	 * but for many applications it will make sense to override this method and create an identity,
	 * if required, based on more specific needs.
	 *
	 * @param   JUser  $identity  An optional identity object. If omitted, the factory user is created.
	 *
	 * @return  JApplicationBase This method is chainable.
	 *
	 * @since   12.1
	 */
	public function loadIdentity(JUser $identity = null)
	{
		$this->identity = ($identity === null) ? JFactory::getUser() : $identity;

		return $this;
	}

	/**
	 * Method to run the application routines.  Most likely you will want to instantiate a controller
	 * and execute it, or perform some sort of task directly.
	 *
	 * @return  void
	 *
	 * @since   3.4 (CMS)
	 */
	protected function doExecute()
	{
		return;
	}

}
