<?php
/**
 * Part of the Joomla Framework Plugin Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Joomla\Plugin;

use Joomla\Event\Dispatcher;
use Joomla\Event\EventInterface;

/**
 * Lazy loading plugin dispatcher, which loads the listeners from a
 * PluginFactoryInterface when the event is fired the first time.
 */
class PluginDispatcher extends Dispatcher
{

	/** @var PluginFactoryInterface **/
	private $factory;

	/** @var string[] **/
	private $loadedEvents = [];

	public function __construct (PluginFactoryInterface $factory)
	{
		$this->factory = $factory;
	}

	public function dispatch ($name, EventInterface $event = null)
	{
		if (! key_exists($name, $this->loadedEvents))
		{
			$this->loadPluginListeners($name, $this->factory->getPlugins());
			$this->loadedEvents[$name] = $name;
		}

		return parent::dispatch($name, $event);
	}

	/**
	 * Loads the listeners from the given plugins and attaches them.
	 *
	 * @param string $name
	 * @param \Joomla\Renderer\PluginInterface[] $plugins
	 */
	private function loadPluginListeners ($name, array $plugins)
	{
		foreach ($plugins as $plugin)
		{
			foreach ($plugin->getListeners($name) as $listener)
			{
				$this->addListener($name, $listener);
			}
		}
	}
}
