<?php
/**
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Joomla\Service;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\Dispatcher;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use Symfony\Component\Yaml\Yaml;

class EventDispatcherServiceProvider implements ServiceProviderInterface
{

	private $key = 'EventDispatcher';

	public function register (Container $container, $alias = null)
	{
		$container->set($this->key, [
				$this,
				'createDispatcher'
		], true, true);

		if (! empty($alias))
		{
			$container->alias($alias, $this->key);
		}
	}

	public function createDispatcher (Container $container)
	{
		$dispatcher = new Dispatcher();

		// @todo Needs to be refactored with a lazy loading plugin manifest
		// reader
		$fs = $container->has('JPATH_ROOT') ? $container->get('JPATH_ROOT') : null;
		if (! $fs && $container->has('config') && $container->get('config')->get('JPATH_ROOT'))
		{
			$fs = $container->get('config')->get('JPATH_ROOT');
		}
		if (is_string($fs))
		{
			// It is only the path
			$fs = new Local($fs);
		}

		if ($fs instanceof AdapterInterface)
		{
			foreach ($fs->listContents('plugins', true) as $file)
			{
				if (strpos($file['path'], 'plugin.yml') === false)
				{
					continue;
				}

				$config = Yaml::parse($fs->read($file['path'])['contents'], true);
				if (key_exists('listeners', $config))
				{
					foreach ($config['listeners'] as $listener)
					{
						$listenerInstance = new $listener['class']();
						foreach ($listener['events'] as $eventName => $method)
						{
							$dispatcher->addListener($eventName, [
									$listenerInstance,
									$method
							]);
						}
					}
				}
			}
		}

		return $dispatcher;
	}
}
