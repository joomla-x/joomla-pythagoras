<?php
/**
 * Part of the Joomla Framework Plugin Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Joomla\Plugin;

/**
 * Plugin Factory Interface
 *
 * @package Joomla/Plugin
 *
 * @since 1.0
 */
interface PluginFactoryInterface
{

	/**
	 * Returns an array of PluginInterface's for the given group.
	 * If the group is not defined all plugins are returned.
	 *
	 * @param string $groupName
	 * @return \Joomla\Plugin\PluginInterface[]
	 */
	public function getPlugins ($groupName = '');
}