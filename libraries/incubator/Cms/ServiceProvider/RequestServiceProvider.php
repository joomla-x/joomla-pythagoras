<?php
/**
 * @package     Joomla.Cms
 * @subpackage  Service Provider Layer
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cms\ServiceProvider;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Http\ServerRequestFactory;

/**
 * Request Service Provider.
 *
 * @package Joomla/Cms
 *
 * @since   __DEPLOY_VERSION__
 */
class RequestServiceProvider implements ServiceProviderInterface
{
	/**
	 * @param   Container $container The DI container
	 * @param   string    $alias     An optional alias
	 *
	 * @return  void
	 */
	public function register(Container $container, $alias = null)
	{
		$container->set(
			'Request',
			[
					$this,
					'createRequest'
			],
			true,
			true
		);

		if ($alias)
		{
			$container->alias($alias, 'Request');
		}
	}

	public function createRequest(Container $container)
	{
		return ServerRequestFactory::fromGlobals();
	}
}
