<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Definition\Locator;

/**
 * Interface LocatorInterface
 *
 * @package  joomla/orm
 * @since    1.0
 */
interface LocatorInterface
{
    /**
     * Find the description file for an entity
     *
     * @param   string $entityName The name of the entity
     *
     * @return  string  Path to the XML file
     */
    public function findFile($entityName);
}
