<?php
/**
 * @package     Joomla.Framework
 * @subpackage  Service Layer
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Service;

use League\Tactician\Middleware;

/**
 * Tactician middleware for dispatching domain events.
 *
 * @package  Joomla/Service
 *
 * @since    __DEPLOY_VERSION__
 */
class DomainEventMiddleware implements Middleware
{
    /** @var object The dispatcher */
    private $dispatcher = null;

    /**
     * Command bus callable.
     *
     * This is needed because the command bus has not been built
     * at the time this middleware object is constructed.
     *
     * @var callable
     */
    private $commandBusCallable = null;

    /** @var CommandBus */
    private $commandBus = null;

    /**
     * Constructor.
     *
     * Note that we take a closure that will return the command bus because at
     * the time this object is constructed the command bus has not be built.
     *
     * @param   object   $dispatcher         An event dispatcher.
     * @param   callable $commandBusCallable A closure that will return the command bus.
     *
     * @since   __DEPLOY_VERSION__
     */
    public function __construct($dispatcher, callable $commandBusCallable)
    {
        $this->dispatcher         = $dispatcher;
        $this->commandBusCallable = $commandBusCallable;
    }

    /**
     * Decorator.
     *
     * Calls the inner handler then dispatches any domain events raised.
     *
     * Suppose there is a DomainEvent with the class name 'PrefixEventSuffix',
     * then you can register listeners for the event using:-
     *   1. A closure.  Example:
     *          $dispatcher->register('onPrefixEventSuffix', function($event) { echo 'Do something here'; });
     *   2. A callback function or method.  Example:
     *          $dispatcher->register('onPrefixEventSuffix', array('MyClass', 'MyMethod'));
     *   3. A preloaded or autoloadable class called 'PrefixEventListenerSuffix' with a method
     *      called 'onPrefixEventSuffix'.
     *   4. An installed and enabled Joomla plugin in the 'domainevent' group, with a method
     *      called 'onPrefixEventSuffix'.
     *
     * In all cases the method called will be passed the event object as its only argument.
     *
     * @param   object   $message A message object (Command or Query).
     * @param   callable $next    Inner middleware object being decorated.
     *
     * @return  mixed
     *
     * @since   __DEPLOY_VERSION__
     */
    public function execute($message, callable $next)
    {
        $accumulatedEvents = [];

        // Pass the message to the next inner layer of middleware.
        $return = $next($message);

        /*
		 * Only publish domain events after completion of a Command.
		 * This is so that queries may be executed during Command execution
		 * without inadvertently publishing raised Domain Events before the
		 * Command has finished executing.
		 */
        if (!($message instanceof Command)) {
            return $return;
        }

        /*
		 * Normally, we expect a possibly empty array of events,
		 * but if we don't get an array, then bubble an empty array up.
		 */
        if (!is_array($return)) {
            return $accumulatedEvents;
        }

        // Resolve the command bus callable to get the command bus.
        $this->commandBus = call_user_func($this->commandBusCallable);

        // Recursively publish any domain events that were raised.
        do {
            // Accumulate all events raised.
            $accumulatedEvents = array_merge($accumulatedEvents, $return);

            // Publish the events.
            $return = $this->innerEventLoop($return);
        } while (!empty($return));

        return true;
    }

    /**
     * Inner event loop.
     *
     * Each event listener might raise further events which need
     * to be passed back into the event loop for publishing.
     *
     * @param   array $events Array of domain event objects.
     *
     * @return  array of newly-raised domain event objects.
     *
     * @since   __DEPLOY_VERSION__
     */
    private function innerEventLoop($events)
    {
        $collectedEvents = [];

        foreach ($events as $event) {
            // Ignore anything that isn't actually an event, just in case.
            if (!($event instanceof DomainEvent)) {
                continue;
            }

            // Import plugins in the domain event group.
            \JPluginHelper::importPlugin('domainevent');

            // Get the name of the event.
            $eventClassReflection = new \ReflectionClass($event);
            $eventClassName       = $eventClassReflection->getShortName();

            // Determine the event name.
            $eventName = 'on' . $eventClassName;

            // Register by convention.
            $this->registerByConvention($eventClassName, $eventName);

            // Publish the event to all registered listeners.
            $results = $this->dispatcher->trigger($eventName, [$event, $this->commandBus]);

            // Merge results into collected events array.
            foreach ($results as $result) {
                $collectedEvents = array_merge($collectedEvents, $result);
            }
        }

        return $collectedEvents;
    }

    /**
     * Register a domain event listener by convention.
     *
     * Replaces "Event" by "EventListener" in the domain event class name
     * and registers that class as a listener.
     *
     * @param   string $eventClassName Name of the domain event class.
     * @param   string $eventName      Name of the event trigger.
     *
     * @return  void
     *
     * @since   __DEPLOY_VERSION__
     */
    private function registerByConvention($eventClassName, $eventName)
    {
        // The domain event class name must contain the substring "Event".
        if (stripos($eventClassName, 'event') === false) {
            return;
        }

        // Determine the event handler class name.
        $handlerClassName = '\\' . str_replace('Event', 'EventListener', $eventClassName);

        // If the event handler class exists, then register it.
        if (class_exists($handlerClassName)) {
            $this->dispatcher->register($eventName, [$handlerClassName, $eventName]);
        }
    }
}
