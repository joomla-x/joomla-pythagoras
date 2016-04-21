<?php
/**
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Joomla\Service;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Plugin\FilePluginFactory;

class PluginFactoryServiceProvider implements ServiceProviderInterface
{

	private $key = 'PluginFactory';

	public function register (Container $container, $alias = null)
	{
		$container->set($this->key, [
				$this,
				'createPluginFactory'
		], true, true);

		if (! empty($alias))
		{
			$container->alias($alias, $this->key);
		}
	}

	public function createPluginFactory (Container $container)
	{
		return new FilePluginFactory($container->get('ConfigDirectory') . '/plugins');
	}
}
