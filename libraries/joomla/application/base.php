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
use Joomla\Registry\Registry;
use Joomla\Input\Input;

/**
 * Joomla Platform Base Application Class
 *
 * @property-read  Input  $input  The application input object
 *
 * @since  12.1
 */
abstract class JApplicationBase extends AbstractApplication
{
	/**
	 * The application identity object.
	 *
	 * @var    JUser
	 * @since  12.1
	 */
	protected $identity;

	/**
	 * Class constructor.
	 *
	 * @param   Input    $input   An optional argument to provide dependency injection for the application's
	 *                             input object.  If the argument is a Input object that object will become
	 *                             the application's input object, otherwise a default input object is created.
	 * @param   Registry  $config  An optional argument to provide dependency injection for the application's
	 *                             config object.  If the argument is a Registry object that object will become
	 *                             the application's config object, otherwise a default config object is created.
	 *
	 * @since   12.1
	 */
	public function __construct(Input $input = null, Registry $config = null)
	{
		parent::__construct($input, $config);

		// Add the dispatcher to the container
		$this->getContainer()->set('dispatcher', function() { return new Dispatcher(); }, true, false);
	}

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
		if ($this->getContainer()->get('dispatcher') instanceof DispatcherInterface)
		{
			$this->getContainer()->get('dispatcher')->addListener($event, $handler);
		}

		return $this;
	}

	/**
	 * Calls all handlers associated with an event group.
	 *
	 * This is a legacy method, implementing old-style (Joomla! 3.x) plugin calls. It's best to go directly through the
	 * Dispatcher and handle the returned EventInterface object instead of going through this method. This method is
	 * deprecated and will be removed in Joomla! 5.x.
	 *
	 * This method will only return the 'result' argument of the event
	 *
	 * @param   string        $eventName  The event name.
	 * @param   array|Event   $args       An array of arguments or an Event object (optional).
	 *
	 * @return  array   An array of results from each function call, or null if no dispatcher is defined.
	 *
	 * @since   12.1
	 * @deprecated
	 */
	public function triggerEvent($eventName, $args = null)
	{
		$dispatcher = $this->getContainer()->get('dispatcher');

		if ($dispatcher instanceof DispatcherInterface)
		{
			if ($args instanceof Event)
			{
				$event = $args;
			}
			else
			{
				if (is_null($args))
				{
					$args = [];
				}

				$event = new Event($eventName, $args);
			}

			$result = $dispatcher->dispatch($eventName, $event);

			// TODO - There are still test cases where the result isn't defined, temporarily leave the isset check in place
			return !isset($result['result']) || is_null($result['result']) ? [] : $result['result'];
		}

		return null;
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
