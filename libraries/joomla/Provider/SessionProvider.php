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
 * The Joomla session provider which serves inputs.
 *
 * @since  4.0
 */
class SessionProvider implements ServiceProviderInterface
{
	public function register(Container $container)
	{
		// Setting the callables for the session
		$container->set('JSession', array($this, 'getSession'));
	}

	/**
	 * Returns the session based on the config in the container.
	 *
	 * @param Container $container
	 *
	 * @return JDatabaseDriver
	 */
	public function getSession(Container $container)
	{
		$config = $container->get('config');

		// Generate a session name.
		$name = md5($config->get('secret') . $config->get('session_name', get_class($config)));

		// Calculate the session lifetime.
		$lifetime = (($config->get('sess_lifetime')) ? $config->get('sess_lifetime') * 60 : 900);

		// Get the session handler from the configuration.
		$handler = $config->get('sess_handler', 'none');

		// Initialize the options for JSession.
		$options = array(
				'name' => $name,
				'expire' => $lifetime,
				'force_ssl' => $config->get('force_ssl')
		);

		return JSession::getInstance($handler, $options);
	}
}