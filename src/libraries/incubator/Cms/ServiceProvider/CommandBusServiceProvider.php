<?php
/**
 * @package     Joomla.Framework
 * @subpackage  Service Layer
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cms\ServiceProvider;

use Joomla\Cms\Service\ExtensionQueryMiddleware;
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
		$container->set(
			'CommandBus',
			[
				$this,
				'createCommandBus'
			],
			true,
			true
		);

		if ($alias)
		{
			$container->alias($alias, 'CommandBus');
		}
	}

	/**
	 * @param   Container  $container  The container
	 *
	 * @return  \Joomla\Service\CommandBus
	 */
	public function createCommandBus(Container $container)
	{
		// Construct the command handler middleware
		$middleware = [];

		if ($container->has('CommandBusMiddleware'))
		{
			$middleware = (array) $container->get('CommandBusMiddleware');
		}

		if ($container->has('extension_factory'))
		{
			$middleware[] = new ExtensionQueryMiddleware($container->get('extension_factory'));
		}

		$builder    = new CommandBusBuilder($container->get('EventDispatcher'));
		$middleware = array_merge($middleware, $builder->getMiddleware());
		$builder->setMiddleware($middleware);

		return $builder->getCommandBus();
	}
}
