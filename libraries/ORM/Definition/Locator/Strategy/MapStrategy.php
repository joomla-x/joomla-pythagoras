<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Definition\Locator\Strategy;

/**
 * Class MapStrategy
 *
 * @package  joomla/orm
 * @since    1.0
 */
class MapStrategy implements StrategyInterface
{
    /** @var  array  The map */
    private $map;

    /**
     * Constructor
     *
     * @param   string $map The map assigning paths to entity names
     */
    public function __construct($map)
    {
        $this->map = $map;
    }

    /**
     * Locate a definition file
     *
     * @param   string $filename The name of the XML file
     *
     * @return  string|null  The path, if found, null else
     */
    public function locate($filename)
    {
        $basename = basename($filename);

        if (isset($this->map[$basename])) {
            return $this->map[$basename];
        }

        return null;
    }
}
