<?php

/**
 * @package     Joomla.Framework
 * @subpackage  Service Layer
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cms\ServiceProvider;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Service\CommandBusBuilder;

/**
 * Command Bus Service Provider.
 *
 * @package Joomla/Service
 *
 * @since   __DEPLOY_VERSION__
 */
class CommandBusServiceProvider implements ServiceProviderInterface
{
	/**
	 * @param   Container $container The DI container
	 * @param   string    $alias     An optional alias
	 *
	 * @return  void
	 */
	public function register(Container $container, $alias = null)
	{
		// Construct the command handler middleware
		$middleware = [];

		if ($container->has('CommandBusMiddleware'))
		{
			$middleware = (array) $container->get('CommandBusMiddleware');
		}

		$builder    = new CommandBusBuilder($container->has('EventDispatcher') ? $container->get('EventDispatcher') : null);
		$middleware = array_merge($middleware, $builder->getMiddleware());
		$builder->setMiddleware($middleware);

		$container->set('CommandBus', $builder->getCommandBus());

		if ($alias)
		{
			$container->alias($alias, 'CommandBus');
		}
	}
}
