<?php
/**
 * @package     Joomla.Framework
 * @subpackage  Service Layer
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Service;

use Joomla\Event\DispatcherInterface;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\CommandNameExtractor\CommandNameExtractor;
use League\Tactician\Handler\Locator\CallableLocator;
use League\Tactician\Handler\Locator\HandlerLocator;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use League\Tactician\Handler\MethodNameInflector\MethodNameInflector;
use League\Tactician\Middleware;

/**
 * Command Bus Builder.
 *
 * Builds a command bus with the specified configuration and middleware.
 *
 * Unless overridden, this will return a default command bus with the following configuration:-
 *        Command name extractor: ClassNameExtractor
 *        Handler locator:        Callable which replaces "Command" with "CommandHandler" and
 *                                "Query" with "QueryHandler" in the command name.
 *        Method name inflector:  HandleInflector
 *        Middleware stack:       CommandLockingMiddleware which locks only for Commands, not Queries.
 *                                DomainEventMiddleware which publishes all DomainEvents, provided
 *                                that a dispatcher was specified when the builder was instantiated.
 *
 * This class also helps isolate our dependency on a particular command bus implementation.
 *
 * @package  Joomla/Service
 *
 * @since    __DEPLOY_VERSION__
 */
class CommandBusBuilder
{
	// Command name extractor.
	private $commandNameExtractor = null;

	// Handler locator.
	private $handlerLocator = null;

	// Method name inflector.
	private $methodNameInflector = null;

	// Middleware stack.
	private $middleware = [];

	/**
	 * Constructor.
	 *
	 * @param   DispatcherInterface $dispatcher Optional domain event dispatcher.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function __construct(DispatcherInterface $dispatcher = null)
	{
		// Set the default command name extractor.
		$this->commandNameExtractor = new ClassNameExtractor;

		// Set the default handler locator.
		$this->handlerLocator = new CallableLocator(
			function ($commandName) use ($dispatcher)
			{
				// Break apart the fully-qualified class name.
				// We do this so that the namespace path is not modified.
				$parts = explode('\\', $commandName);

				// Get the class name only.
				$className = array_pop($parts);

				// Determine the handler class name from the command class name.
				$handlerName = str_replace('Command', 'CommandHandler', $className);
				$handlerName = str_replace('Query', 'QueryHandler', $handlerName);

				// Construct the fully-qualified class name of the handler.
				$serviceName = implode('\\', $parts) . '\\' . $handlerName;

				return new $serviceName($this->getCommandBus(), $dispatcher);
			}
		);

		// Set the default method name inflector.
		$this->methodNameInflector = new HandleInflector;

		// Default middleware starts with the conditional command locking plugin.
		$this->middleware[] = new CommandLockingMiddleware;

		// If we have a dispatcher, we can also add the domain event publishing plugin.
		if (!is_null($dispatcher))
		{
			$this->middleware[] = new DomainEventMiddleware(
				$dispatcher,
				function ()
				{
					return $this->getCommandBus();
				}
			);
		}
	}

	/**
	 * Builds and returns the specified command bus.
	 *
	 * @return  CommandBus
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getCommandBus()
	{
		// Construct the command handler middleware from its three elements.
		$handlerMiddleware = new CommandHandlerMiddleware(
			$this->commandNameExtractor,
			$this->handlerLocator,
			$this->methodNameInflector
		);

		// Add the command handler middleware to the end of the middleware array.
		$this->middleware[] = $handlerMiddleware;

		return new CommandBus($this->middleware);
	}

	/**
	 * Get the middleware stack.
	 *
	 * This allows the building program to manipulate the default stack, for example.
	 *
	 * @return  Middleware[]
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function getMiddleware()
	{
		return $this->middleware;
	}

	/**
	 * Set the command name extractor, overriding the default.
	 *
	 * @param   CommandNameExtractor $commandNameExtractor Command name extractor.
	 *
	 * @return  CommandBusBuilder  This object for method chaining.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function setCommandNameExtractor(CommandNameExtractor $commandNameExtractor)
	{
		$this->commandNameExtractor = $commandNameExtractor;

		return $this;
	}

	/**
	 * Set the handler locator, overriding the default.
	 *
	 * @param   HandlerLocator $handlerLocator Handler locator.
	 *
	 * @return  CommandBusBuilder  This object for method chaining.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function setHandlerLocator(HandlerLocator $handlerLocator)
	{
		$this->handlerLocator = $handlerLocator;

		return $this;
	}

	/**
	 * Set the method name inflector, overriding the default.
	 *
	 * @param   MethodNameInflector $methodNameInflector Method name inflector.
	 *
	 * @return  CommandBusBuilder  This object for method chaining.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function setMethodNameInflector(MethodNameInflector $methodNameInflector)
	{
		$this->methodNameInflector = $methodNameInflector;

		return $this;
	}

	/**
	 * Set the middleware stack, overriding or modifying the default stack.
	 *
	 * @param   Middleware[] $middleware An array of Middleware objects.
	 *
	 * @return  CommandBusBuilder  This object for method chaining.
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	public function setMiddleware(array $middleware = [])
	{
		$this->middleware = $middleware;

		return $this;
	}
}
