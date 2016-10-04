<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Service;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Logging\DebugStack;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\ORM\Storage\Doctrine\DoctrineTransactor;

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
		$container->set('Repository', [$this, 'createRepositoryFactory'], true, true);

		if (!empty($alias))
		{
			$container->alias($alias, 'Repository');
		}
	}

	/**
	 * Creates a RepositoryFactory
	 *
	 * @param   Container  $container  The container
	 *
	 * @return  RepositoryFactory
	 */
	public function createRepositoryFactory(Container $container)
	{
		$config = parse_ini_file(JPATH_ROOT . '/config/database.ini', true);

		$configuration = new Configuration;

		// Add logger
		$logger = new DebugStack;
		$configuration->setSQLLogger($logger);

		$connection = DriverManager::getConnection(['url' => $config['databaseUrl']], $configuration);
		$transactor = new DoctrineTransactor($connection);

		$repositoryFactory = new RepositoryFactory($config, $connection, $transactor);
		$repositoryFactory->setDispatcher($container->get('dispatcher'));

		return $repositoryFactory;
	}
}
