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
use Mockery\Generator\Method;

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
	 * Registers a RepositoryFactory with the container
	 *
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
	 * Creates a RepositoryFactory
	 *
	 * @param   Container  $container  The container
	 *
	 * @return  void
	 */
	public function createRepositoryFactory(Container $container)
	{
		throw new \RuntimeException(__METHOD__ . ' is not yet implemented');
	}
}
