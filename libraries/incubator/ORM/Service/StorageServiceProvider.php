<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Service;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * Storage Service Provider.
 *
 * @package Joomla/ORM
 *
 * @since   __DEPLOY_VERSION__
 */
class StorageServiceProvider implements ServiceProviderInterface
{
	/**
	 * @param   Container $container The DI container
	 * @param   string    $alias     An optional alias
	 *
	 * @return  void
	 */
	public function register(Container $container, $alias = null)
	{
		if (!empty($alias))
		{
			throw new \RuntimeException('The StorageService does not support aliases.');
		}

		$container->set('Repository', [$this, 'createRepositoryFactory'], true, true);
	}

	/**
	 * @param   Container  $container  The container
	 *
	 * @return  \Joomla\Service\CommandBus
	 */
	public function createRepositoryFactory(Container $container)
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
