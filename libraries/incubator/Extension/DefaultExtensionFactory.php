<?php
/**
 * Part of the Joomla Framework Extension Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Extension;

use Interop\Container\ContainerInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class DefaultExtensionFactory
 *
 * @package Joomla\Extension
 *
 * @since   1.0
 */
class DefaultExtensionFactory implements ExtensionFactoryInterface
{
	/** @var string  The root folder the factory reads the extensions from. */
	private $rootFolder;

	/** @var ExtensionInterface[][] Extensions cache. */
	private $extensions = [];

	/** @var ContainerInterface  The container */
	private $container;

	/**
	 * DefaultExtensionFactory constructor.
	 *
	 * @param   string             $rootFolder The root folder the factory reads the extensions from
	 * @param   ContainerInterface $container  The container
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

		$ini        = $this->rootFolder . '/config/extensions.ini';
		$extensions = file_exists($ini) ? parse_ini_file($ini) : [];

		foreach ($extensions as $extension => $path)
		{
			$file = $path . '/config/extension.yml';

			if (!file_exists($file))
			{
				continue;
			}

			$extension = new Extension;

			$config = Yaml::parse(file_get_contents($file), Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE);

			if (key_exists('listeners', $config))
			{
				$this->createListeners($extension, $config['listeners']);
			}

			if (key_exists('queryhandlers', $config))
			{
				$this->createQueryHandlers($extension, $config['queryhandlers']);
			}

			$this->extensions[$group][] = $extension;
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
