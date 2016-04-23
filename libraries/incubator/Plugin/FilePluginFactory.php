<?php
/**
 * Part of the Joomla Framework Plugin Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Plugin;

use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class FilePluginFactory
 *
 * @package Joomla\Plugin
 *
 * @since  1.0
 */
class FilePluginFactory implements PluginFactoryInterface
{
	/** @var string|AdapterInterface  The root folder the factory reads the plugins from. */
	private $rootFolder;

	/** @var PluginInterface[] Plugins cache. */
	private $plugins = [];

	/** @var array the loaded files */
	private $loadedFiles = [];

	/**
	 * FilePluginFactory constructor.
	 *
	 * @param   string|AdapterInterface $rootFolder  The root folder the factory reads the plugins from
	 */
	public function __construct($rootFolder)
	{
		$this->rootFolder = $rootFolder;
	}

	/**
	 * Get the plugins
	 *
	 * @param   string $group  The plugin group
	 *
	 * @return  PluginInterface[]
	 */
	public function getPlugins($group = '')
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

		if (!$fs instanceof AbstractAdapter)
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

			$plugin                   = new Plugin;
			$this->loadedFiles[$path] = $plugin;
			$this->plugins[$group][]  = $plugin;

			$config = Yaml::parse($fs->read($file['path'])['contents'], true);

			if (key_exists('listeners', $config))
			{
				$this->createListeners($plugin, $config['listeners']);
			}
		}

		return $this->plugins[$group];
	}

	/**
	 * Create listeners
	 *
	 * @param   Plugin  $plugin           The plugin
	 * @param   array   $listenersConfig  The configuration
	 *
	 * @return  void
	 */
	private function createListeners(Plugin $plugin, array $listenersConfig)
	{
		foreach ($listenersConfig as $listener)
		{
			$listenerInstance = new $listener['class'];

			foreach ($listener['events'] as $eventName => $method)
			{
				$plugin->addListener(
					$eventName,
					[
						$listenerInstance,
						$method
					]
				);
			}
		}
	}
}
