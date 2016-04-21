<?php
/**
 * Part of the Joomla Framework Plugin Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Joomla\Plugin;

class Plugin implements PluginInterface
{

	private $listeners = [];

	public function getListeners ($eventName)
	{
		if (! key_exists($eventName, $this->listeners))
		{
			return [];
		}
		return $this->listeners[$eventName];
	}

	public function addListener ($eventName, $listener)
	{
		if (! key_exists($eventName, $this->listeners))
		{
			$this->listeners[$eventName] = [];
		}
		$this->listeners[$eventName][] = $listener;
	}
}
