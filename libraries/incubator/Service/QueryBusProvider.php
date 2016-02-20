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
use Joomla\DI\ServiceProviderInterface;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\Locator\CallableLocator;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;

/**
 * Registers a query bus service provider.
 *
 * @since  __DEPLOY__
 */
class QueryBusProvider implements ServiceProviderInterface
{
	/**
	 * Registers the query bus service provider.
	 *
	 * @param   Container $container A dependency injection container.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY__
	 */
	public function register(Container $container)
	{
		$container->share(
			'querybus',
			function (Container $container) {

				$handlerMiddleware = new CommandHandlerMiddleware(
					new ClassNameExtractor,
					new CallableLocator(
						function ($queryName) use ($container) {

							// Break apart the fully-qualified class name.
							// We do this so that the namespace path is not modified.
							$parts = explode('\\', $queryName);

							// Determine the handler class name from the query class name.
							$handlerName = str_replace('Query', 'QueryHandler', array_pop($parts));

							// Construct the fully-qualified class name of the handler.
							$serviceName = implode('\\', $parts) . '\\' . $handlerName;

							return new $serviceName($container);
						}
					),
					new HandleInflector
				);

				$middleware = array(
					$handlerMiddleware
				);

				return new QueryBusBase($middleware);
			},
			true
		);
	}
}
