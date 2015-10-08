<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Service
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Provider;

defined('JPATH_PLATFORM') or die;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * The Joomla database provider which registers databases.
 *
 * @since  4.0
 */
class DatabaseProvider implements ServiceProviderInterface
{
	public function register(Container $container)
	{
		// Setting the callables for the language
		$container->set('JDatabaseDriver', array($this, 'getDatabase'));

		// Registering the protected database object
		$container->set('database', array($this, 'getDatabase'), true, false);
		$container->alias('dbo', 'database');
	}

	/**
	 * Returns a database driver from the config in the container.
	 *
	 * @param Container $container
	 *
	 * @return JDatabaseDriver
	 */
	public function getDatabase(Container $container)
	{
		$conf = $container->get('config');

		$host = $conf->get('host');
		$user = $conf->get('user');
		$password = $conf->get('password');
		$database = $conf->get('db');
		$prefix = $conf->get('dbprefix');
		$driver = $conf->get('dbtype');
		$debug = $conf->get('debug');

		$options = array('driver' => $driver, 'host' => $host, 'user' => $user, 'password' => $password, 'database' => $database, 'prefix' => $prefix);

		try
		{
			$db = \JDatabaseDriver::getInstance($options);
		}
		catch (RuntimeException $e)
		{
			if (!headers_sent())
			{
				header('HTTP/1.1 500 Internal Server Error');
			}

			jexit('Database Error: ' . $e->getMessage());
		}

		$db->setDebug($debug);

		return $db;
	}
}