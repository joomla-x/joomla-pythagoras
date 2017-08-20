<?php
/**
 * @package     Joomla.Framework
 * @subpackage  Service Layer
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Service;

use Joomla\Event\DispatcherAwareTrait;
use Joomla\Event\DispatcherInterface;

/**
 * Abstract base class for command handlers.
 *
 * Supports handling of domain events.  This would be better implemented
 * as a trait, but traits are not implemented until PHP 5.4.0.
 *
 * @package  Joomla/Service
 *
 * @since    __DEPLOY_VERSION__
 */
abstract class CommandHandler
{
    use DispatcherAwareTrait;

    /** @var CommandBus The command bus */
    private $commandBus = null;

    /** @var array The domain events */
    private $pendingEvents = array();

    /**
     * Constructor.
     *
     * @param   CommandBus          $commandBus A command bus
     * @param   DispatcherInterface $dispatcher A dispatcher
     *
     * @since   __DEPLOY_VERSION__
     */
    public function __construct(CommandBus $commandBus, DispatcherInterface $dispatcher)
    {
        $this->commandBus = $commandBus;
        $this->setDispatcher($dispatcher);
    }

    /**
     * Get the command bus.
     *
     * @return   CommandBus
     */
    public function getCommandBus()
    {
        return $this->commandBus;
    }

    /**
     * Release all pending domain events.
     *
     * As a convenience, a new event can also be raised at the same time.
     *
     * @param   DomainEvent $event An event to be raised.
     *
     * @return  array of DomainEvent objects.
     *
     * @since   __DEPLOY_VERSION__
     */
    public function releaseEvents($event = null)
    {
        if ($event instanceof DomainEvent) {
            $this->raiseEvent($event);
        }

        $events              = $this->pendingEvents;
        $this->pendingEvents = array();

        return $events;
    }

    /**
     * Raise a domain event.
     *
     * @param   DomainEvent $event Domain event object.
     *
     * @return  void
     *
     * @since   __DEPLOY_VERSION__
     */
    public function raiseEvent(DomainEvent $event)
    {
        $this->pendingEvents[] = $event;
    }
}
