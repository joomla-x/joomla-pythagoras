<?php
/**
 * Part of the Joomla Framework Plugin Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Plugin;

/**
 * Plugin Interface
 *
 * @package Joomla/Plugin
 *
 * @since   1.0
 */
interface PluginInterface
{
	/**
	 * Returns an array of callables to listen for events on a
	 * DispatcherInterface.
	 *
	 * @param   string $eventName  The name of the event
	 *
	 * @return  callable[]
	 *
	 * @see \Joomla\Event\DispatcherInterface::addListener
	 * @see \Joomla\Event\DispatcherInterface::dispatch
	 */
	public function getListeners($eventName);
}
