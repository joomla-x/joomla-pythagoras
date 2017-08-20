<?php
/**
 * Part of the Joomla Framework Extension Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Extension;

/**
 * Extension Factory Interface
 *
 * @package Joomla/Extension
 *
 * @since   1.0
 */
interface ExtensionFactoryInterface
{
    /**
     * Returns an array of ExtensionInterface's for the given group.
     * If the group is not defined all plugins are returned.
     *
     * @param   string $groupName The name of the plugin group
     *
     * @return  ExtensionInterface[]
     */
    public function getExtensions($groupName = '');
}
