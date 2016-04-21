<?php
/**
 * Part of the Joomla Framework DI Package
 *
 * @copyright  Copyright (C) 2013 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Joomla\DI\Loader;
use Joomla\DI\Container;
use Symfony\Component\Yaml\Yaml;

/**
 * Fills a container with service providers from a yaml string.
 */
class YamlLoader implements LoaderInterface
{

	private $container = null;

	public function __construct (Container $container)
	{
		$this->container = $container;
	}

	/**
	 * Helper function to load the services from a file.
	 *
	 * @param string $filename
	 * @see LoaderInterface::load()
	 */
	public function loadFromFile ($filename)
	{
		$this->load(file_get_contents($filename));
	}

	public function load ($content)
	{
		$data = Yaml::parse($content);

		if (! key_exists('providers', $data))
		{
			return;
		}
		foreach ($data['providers'] as $alias => $service)
		{
			if (! class_exists($service['class']))
			{
				continue;
			}

			$arguments = [];
			if (key_exists('arguments', $service))
			{
				foreach ($service['arguments'] as $argument)
				{
					if (! $this->container->has($argument))
					{
						continue;
					}

					$arguments[] = $this->container->get($argument);
				}
			}

			$reflect = new \ReflectionClass($service['class']);

			$this->container->registerServiceProvider($reflect->newInstanceArgs($arguments), $alias);
		}
	}
}