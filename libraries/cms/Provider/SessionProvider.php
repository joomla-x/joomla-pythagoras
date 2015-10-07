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

/**
 * The Cms session provider which serves inputs.
 *
 * @since  4.0
 */
class SessionProvider implements ServiceProviderInterface
{

	public function register(Container $container)
	{
		$container->set('JSession', array($this, 'getSession'));
	}

	public function getSession(Container $container)
	{
		$config = $container->get('config');
		$app = $container->get('app');

		// Generate a session name.
		$name = md5($config->get('secret') . $config->get('session_name', get_class($app)));

		// Calculate the session lifetime.
		$lifetime = (($config->get('lifetime')) ? $config->get('lifetime') * 60 : 900);


		// Initialize the options for JSession.
		$options = array(
			'name'   => $name,
			'expire' => $lifetime
		);

		switch ($app->getClientId())
		{
			case 0:
				if ($config->get('force_ssl') == 2)
				{
					$options['force_ssl'] = true;
				}

				break;

			case 1:
				if ($config->get('force_ssl') >= 1)
				{
					$options['force_ssl'] = true;
				}

				break;
		}

		// Get the Joomla configuration settings
		$handler = $config->get('session_handler', 'none');

		// Config time is in minutes
		$options['expire'] = ($config->get('lifetime')) ? $config->get('lifetime') * 60 : 900;

		$sessionHandler = new \JSessionHandlerJoomla($options, $config);
		return \JSession::getInstance($handler, $options, $sessionHandler);
	}
}