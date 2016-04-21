<?php
/**
 * Part of the Joomla Framework Plugin Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Joomla\Plugin;

use League\Flysystem\AdapterInterface;
use Symfony\Component\Yaml\Yaml;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Adapter\AbstractAdapter;

class FilePluginFactory implements PluginFactoryInterface
{

	/**
	 * The root folder the factory reads the plugins from.
	 *
	 * @var string|AdapterInterface
	 */
	private $rootFolder;

	/**
	 * Plugins cache.
	 *
	 * @var PluginInterface[]
	 */
	private $plugins = [];

	private $loadedFiles = [];

	public function __construct ($rootFolder)
	{
		$this->rootFolder = $rootFolder;
	}

	public function getPlugins ($group = '')
	{
		if (key_exists($group, $this->plugins))
		{
			return $this->plugins[$group];
		}
		$this->plugins[$group] = [];

		$fs = $this->rootFolder;
		if (is_string($this->rootFolder))
		{
			// It is only the path
			$fs = new Local($this->rootFolder);
		}
		if (! $fs instanceof AbstractAdapter)
		{
			return [];
		}

		foreach ($fs->listContents($group, true) as $file)
		{
			if (strpos($file['path'], 'plugin.yml') === false)
			{
				continue;
			}

			$path = $fs->applyPathPrefix($file['path']);
			if (key_exists($path, $this->loadedFiles))
			{
				// We have loaded it already
				$this->plugins[$group][] = $this->loadedFiles[$path];
				continue;
			}

			$plugin = new Plugin();
			$this->loadedFiles[$path] = $plugin;
			$this->plugins[$group][] = $plugin;

			$config = Yaml::parse($fs->read($file['path'])['contents'], true);
			if (key_exists('listeners', $config))
			{
				$this->createListeners($plugin, $config['listeners']);
			}
		}

		return $this->plugins[$group];
	}

	private function createListeners (Plugin $plugin, array $listenersConfig)
	{
		foreach ($listenersConfig as $listener)
		{
			$listenerInstance = new $listener['class']();
			foreach ($listener['events'] as $eventName => $method)
			{
				$plugin->addListener($eventName, [
						$listenerInstance,
						$method
				]);
			}
		}
	}
}
