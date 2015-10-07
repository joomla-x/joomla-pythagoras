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
use Joomla\Input\Input;

/**
 * The Joomla input provider which serves inputs.
 *
 * @since  4.0
 */
class InputProvider implements ServiceProviderInterface
{
	public function register(Container $container)
	{
		$container->set('Joomla\Input\Input', array($this, 'getInput'));
		$container->alias('Input', 'Joomla\Input\Input');
	}

	/**
	 * Creates an Input object;
	 *
	 * @param Container $container
	 *
	 * @return Joomla\Input\Input
	 */
	public function getInput(Container $container)
	{
		return new Input();
	}

}