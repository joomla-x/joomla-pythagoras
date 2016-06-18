<?php
/**
 * Part of the Joomla Framework DI Package
 *
 * @copyright  Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\DI;

/**
 * Defines the interface for a Service Provider.
 *
 * @since  1.0
 */
interface ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container $container The DI container.
	 * @param   string    $alias     An optional alias for the service
	 *
	 * @return  void
	 */
	public function register(Container $container, $alias = null);
}
