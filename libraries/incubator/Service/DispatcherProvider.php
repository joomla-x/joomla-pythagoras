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
use Joomla\Event\Dispatcher;

/**
 * Registers a domain event publisher service provider.
 *
 * @since  __DEPLOY__
 */
class DispatcherProvider implements ServiceProviderInterface
{
	/**
	 * Registers the domain event publisher service provider.
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
			'dispatcher',
			function (Container $container) {

				return new Dispatcher;
			},
			true
		);
	}
}
