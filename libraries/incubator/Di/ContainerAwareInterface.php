<?php
/**
 * Part of the Joomla Framework DI Package
 *
 * @copyright  Copyright (C) 2013 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Di;

use Interop\Container\Exception\ContainerException;

/**
 * Defines the interface for a Container Aware class.
 *
 * @package  Joomla/di
 *
 * @since    1.0
 */
interface ContainerAwareInterface
{
	/**
	 * Get the DI container.
	 *
	 * @return  Container
	 *
	 * @since   1.0
	 *
	 * @throws  ContainerException May be thrown if the container has not been set.
	 */
	public function getContainer();

	/**
	 * Set the DI container.
	 *
	 * @param   Container $container The DI container.
	 *
	 * @return  mixed
	 *
	 * @since   1.0
	 */
	public function setContainer(Container $container);
}
