<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Service
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Cms\Provider;

defined('JPATH_PLATFORM') or die;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Registry\Registry;

/**
 * The CMS Service provider which serves Regsirty objects and the actual config.
 *
 * @since  4.0
 */
class ConfigurationProvider implements ServiceProviderInterface
{
	public function register(Container $container)
	{
		$container->set('Joomla\Registry\Registry', array($this, 'getRegistry'));
		$container->alias('Registry', 'Joomla\Registry\Registry');

		$container->set('config', array($this, 'getConfig'), false, true);
	}

	/**
	 * Creates a Registry object;
	 *
	 * @param Container $container
	 *
	 * @return Joomla\Registry\Registry
	 */
	public function getRegistry(Container $container)
	{
		return new Registry();
	}

	public function getConfig(Container $container)
	{
		$file = JPATH_PLATFORM . '/config.php';

		if (is_file($file))
		{
			include_once $file;
		}

		// Create the registry with a default namespace of config
		$registry = $container->get('Registry');

		// Build the config name.
		$name = 'JConfig';

		// Handle the PHP configuration type.
		if (class_exists($name))
		{
			// Create the JConfig object
			$config = new $name;

			// Load the configuration values into the registry
			$registry->loadObject($config);
		}

		return $registry;
	}
}