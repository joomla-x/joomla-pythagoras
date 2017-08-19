<?php
/**
 * Part of the Joomla Framework Extension Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Extension;

use Psr\Container\ContainerInterface;
use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class FileExtensionFactory
 *
 * @package Joomla\Extension
 *
 * @since   1.0
 */
class FileExtensionFactory implements ExtensionFactoryInterface
{
	/** @var string|AdapterInterface  The root folder the factory reads the extensions from. */
	private $rootFolder;

	/** @var ExtensionInterface[][] Extensions cache. */
	private $extensions = [];

	/** @var array the loaded files */
	private $loadedFiles = [];

	/** @var ContainerInterface  The container */
	private $container;

	/**
	 * FileExtensionFactory constructor.
	 *
	 * @param   string|AdapterInterface $rootFolder The root folder the factory reads the extensions from
	 * @param   ContainerInterface      $container  The container
	 *
	 * @todo remove the container parameter and pass something which will lazy load the command bus and dispatcher
	 */
	public function __construct($rootFolder, ContainerInterface $container)
	{
		$this->rootFolder = $rootFolder;
		$this->container  = $container;
	}

	/**
	 * Get the extensions
	 *
	 * @param   string $group The extension group
	 *
	 * @return  ExtensionInterface[]
	 */
	public function getExtensions($group = '')
	{
		if (key_exists($group, $this->extensions))
		{
			return $this->extensions[$group];
		}

		$this->extensions[$group] = [];

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
			if (strpos($file['path'], 'extension.yml') === false)
			{
				continue;
			}

			$path = $fs->applyPathPrefix($file['path']);

			if (key_exists($path, $this->loadedFiles))
			{
				// We have loaded it already
				$this->extensions[$group][] = $this->loadedFiles[$path];
				continue;
			}

			$extension                  = new Extension;
			$this->loadedFiles[$path]   = $extension;
			$this->extensions[$group][] = $extension;

			$config = Yaml::parse($fs->read($file['path'])['contents'], Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE);

			if (key_exists('listeners', $config))
			{
				$this->createListeners($extension, $config['listeners']);
			}

			if (key_exists('queryhandlers', $config))
			{
				$this->createQueryHandlers($extension, $config['queryhandlers']);
			}
		}

		return $this->extensions[$group];
	}

	/**
	 * Create listeners
	 *
	 * @param   Extension $extension       The extension
	 * @param   array     $listenersConfig The configuration
	 *
	 * @return  void
	 */
	private function createListeners(Extension $extension, array $listenersConfig)
	{
		foreach ($listenersConfig as $listener)
		{
			$listenerInstance = new $listener['class'];

			foreach ($listener['events'] as $eventName => $method)
			{
				$extension->addListener(
					$eventName,
					[
						$listenerInstance,
						$method
					]
				);
			}
		}
	}

	/**
	 * @param   Extension $extension      The extension
	 * @param   array     $handlersConfig Handler configuration
	 *
	 * @return  void
	 */
	private function createQueryHandlers(Extension $extension, array $handlersConfig)
	{
		foreach ($handlersConfig as $handler)
		{
			$extension->addQueryHandler(
				$handler['query'],
				new $handler['class']($this->container->get('CommandBus'), $this->container->get('EventDispatcher'))
			);
		}
	}
}
