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
 * Class PluginDispatcher
 *
 * Lazy loading plugin dispatcher, which loads the listeners from a
 * PluginFactoryInterface when the event is fired the first time.
 *
 * @package  Joomla\Plugin
 *
 * @since  1.0
 */
class PluginDispatcher extends Dispatcher
{
	/** @var PluginFactoryInterface The plugin factory */
	private $factory;

	/** @var string[] The loaded events */
	private $loadedEvents = [];

	/**
	 * PluginDispatcher constructor.
	 *
	 * @param   PluginFactoryInterface $factory  The plugin factory
	 */
	public function __construct(PluginFactoryInterface $factory)
	{
		$this->factory = $factory;
	}

	/**
	 * Call the plugins
	 *
	 * @param   EventInterface $event  The event
	 *
	 * @return  EventInterface
	 */
	public function dispatch(EventInterface $event)
	{
		$name = $event->getName();

		if (!key_exists($name, $this->loadedEvents))
		{
			$this->loadPluginListeners($name, $this->factory->getPlugins());
			$this->loadedEvents[$name] = $name;
		}

		return parent::dispatch($event);
	}

	/**
	 * Loads the listeners from the given plugins and attaches them.
	 *
	 * @param   string            $name     The event name
	 * @param   PluginInterface[] $plugins  A list of plugins
	 *
	 * @return  void
	 */
	private function loadPluginListeners($name, array $plugins)
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
