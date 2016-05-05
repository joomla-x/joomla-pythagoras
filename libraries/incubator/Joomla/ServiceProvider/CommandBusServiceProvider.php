<?php

/**
 * @package     Joomla.Framework
 * @subpackage  Service Layer
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
namespace Joomla\Joomla\ServiceProvider;
use Joomla\Service\CommandBus;
use Joomla\Service\CommandLockingMiddleware;
use Joomla\DI\ServiceProviderInterface;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\Locator\CallableLocator;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use League\Tactician\Middleware;
use Joomla\DI\Container;

/**
 * Command Bus Service Provider.
 *
 * @package Joomla/Service
 *         
 * @since __DEPLOY_VERSION__
 */
class CommandBusServiceProvider implements ServiceProviderInterface
{

	public function register (Container $container, $alias = null)
	{
		// Construct the command handler middleware
		$middleware = [];
		if ($container->has('CommandBusMiddleware'))
		{
			$middleware = (array) $container->get('CommandBusMiddleware');
		}
		
		// Default middleware starts with the conditional command locking
		// plugin.
		$middleware[] = new CommandLockingMiddleware();
		
		// Add the command handler middleware to the end of the list
		$middleware[] = new CommandHandlerMiddleware(new ClassNameExtractor(), 
				new CallableLocator(
						function  ($commandName) use ( $container) {
							// Break apart the fully-qualified class name.
							// We do this so that the namespace path is not
							// modified.
							$parts = explode('\\', $commandName);
							
							// Get the class name only.
							$className = array_pop($parts);
							
							// Determine the handler class name from the command
							// class name.
							$handlerName = str_replace('Command', 'CommandHandler', $className);
							$handlerName = str_replace('Query', 'QueryHandler', $handlerName);
							
							// Construct the fully-qualified class name of the
							// handler.
							$serviceName = implode('\\', $parts) . '\\' . $handlerName;
							
							return new $serviceName($container->get('CommandBus'));
						}), new HandleInflector());
		
		$container->set('CommandBus', new CommandBus($middleware));
		
		if ($alias)
		{
			$container->alias($alias, 'CommandBus');
		}
	}
}

