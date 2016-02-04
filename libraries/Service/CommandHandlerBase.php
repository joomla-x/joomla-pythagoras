<?php
/**
 * @package     Joomla.Framework
 * @subpackage  Service Layer
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Service;

use Joomla\DI\Container;

/**
 * Base class for command/service handlers.
 * 
 * Supports handling of domain events.  This would be better implemented
 * as a trait, but traits are not implemented until PHP 5.4.0.
 * 
 * @since  __DEPLOY__
 */
class CommandHandlerBase implements CommandHandler
{
	// Container.
	protected $container = null;

	// Domain events.
	private $pendingEvents = array();

	/**
	 * Constructor.
	 * 
	 * @param   Container  $container  A DI container.
	 * 
	 * @since   __DEPLOY__
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Raise a domain event.
	 * 
	 * @param   DomainEvent  $event  Domain event object.
	 * 
	 * @return  void
	 * 
	 * @since   __DEPLOY__
	 */
	public function raiseEvent(Event $event)
	{
		$this->pendingEvents[] = $event;
	}

	/**
	 * Release all pending domain events.
	 * 
	 * As a convenience, a new event can also be raised at the same time.
	 * 
	 * @param   DomainEvent  $event  An event to be raised.
	 * 
	 * @return  array of DomainEvent objects.
	 * 
	 * @since   __DEPLOY__
	 */
	public function releaseEvents($event = null)
	{
		if ($event instanceof Event)
		{
			$this->raiseEvent($event);
		}

		$events = $this->pendingEvents;
		$this->pendingEvents = array();

		return $events;
	}
}
