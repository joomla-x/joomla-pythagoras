<?php
/**
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cms\ServiceProvider;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Plugin\FilePluginFactory;

/**
 * Class PluginFactoryServiceProvider
 *
 * @package Joomla\Cms\ServiceProvider
 *
 * @since  1.0
 */
class PluginFactoryServiceProvider implements ServiceProviderInterface
{
	/** @var string The access key for the container */
	private $key = 'PluginFactory';

	/**
	 * Add the plugin factory to a container
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
				'createPluginFactory'
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
	 * Create the plugin factory
	 *
	 * @param   Container $container  The container
	 *
	 * @return  FilePluginFactory
	 */
	public function createPluginFactory(Container $container)
	{
		return new FilePluginFactory($container->get('ConfigDirectory') . '/plugins');
	}
}
