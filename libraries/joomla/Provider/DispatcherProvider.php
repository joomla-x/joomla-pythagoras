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
use Joomla\Event\Dispatcher;

/**
 * The Joomla dispatcher service provider.
 *
 * @since  4.0
 */
class DispatcherProvider implements ServiceProviderInterface
{
	public function register(Container $container)
	{
		$container->set('Dispatcher', array($this, 'getDispatcher'));
	}

	/**
	 * Creates a Dispatcher object.
	 *
	 * @param Container $container
	 *
	 * @return Joomla\Event\Dispatcher
	 */
	public function getDispatcher(Container $container)
	{
		return new Dispatcher();
	}

}