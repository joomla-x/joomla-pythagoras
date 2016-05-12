<?php
/**
 * Part of the Joomla Framework Extension Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Extension;

use Joomla\Event\Dispatcher;
use Joomla\Event\EventInterface;

/**
 * Class ExtensionDispatcher
 *
 * Lazy loading plugin dispatcher, which loads the listeners from a
 * ExtensionFactoryInterface when the event is fired the first time.
 *
 * @package  Joomla\Extension
 *
 * @since  1.0
 */
class ExtensionDispatcher extends Dispatcher
{
	/** @var ExtensionFactoryInterface The plugin factory */
	private $factory;

	/** @var string[] The loaded events */
	private $loadedEvents = [];

	/**
	 * ExtensionDispatcher constructor.
	 *
	 * @param   ExtensionFactoryInterface $factory  The plugin factory
	 */
	public function __construct(ExtensionFactoryInterface $factory)
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
			$this->loadExtensionListeners($name, $this->factory->getExtensions());
			$this->loadedEvents[$name] = $name;
		}

		return parent::dispatch($event);
	}

	/**
	 * Loads the listeners from the given plugins and attaches them.
	 *
	 * @param   string            $name     The event name
	 * @param   ExtensionInterface[] $plugins  A list of plugins
	 *
	 * @return  void
	 */
	private function loadExtensionListeners($name, array $plugins)
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
