<?php
/**
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Service;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Plugin\PluginDispatcher;

/**
 * Class EventDispatcherServiceProvider
 *
 * @package Joomla\Service
 *
 * @since  1.0
 */
class EventDispatcherServiceProvider implements ServiceProviderInterface
{
	/** @var string  The access key */
	private $key = 'EventDispatcher';

	/**
	 * Add the dispatcher to a container
	 *
	 * @param   Container $container  The container
	 * @param   string    $alias      An optional alias
	 *
	 * @return  void
	 */
	public function register(Container $container, $alias = null)
	{
		$container->set(
			$this->key,
			[
				$this,
				'createDispatcher'
			],
			true,
			true
		);

		if (!empty($alias))
		{
			$container->alias($alias, $this->key);
		}
	}

	/**
	 * Create the dispatcher
	 *
	 * @param   Container $container  The container
	 *
	 * @return  PluginDispatcher
	 */
	public function createDispatcher(Container $container)
	{
		return new PluginDispatcher($container->get('plugin_factory'));
	}
}
