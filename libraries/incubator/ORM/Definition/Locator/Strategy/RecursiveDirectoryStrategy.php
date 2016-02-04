<?php
/**
 * Part of the Joomla Framework ORM Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\ORM\Definition\Locator\Strategy;

/**
 * Class RecursiveDirectoryStrategy
 *
 * @package  joomla/orm
 * @since    1.0
 */
class RecursiveDirectoryStrategy implements StrategyInterface
{
    /** @var  string  The root directory */
    private $root;

    /**
     * Constructor
     *
     * @param   string $root The root directory for the search
     */
    public function __construct($root)
    {
        $this->root = $root;
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
        return $this->scan($this->root, $filename);
    }

    /**
     * Scan a directory for a filename
     *
     * @param   string $directory The start directory
     * @param   string $filename The filename to search for
     *
     * @return  string|null  The path, if found, null else
     */
    private function scan($directory, $filename)
    {
        $filename = strtolower($filename);

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory)) as $file) {
            /** @var \SplFileInfo $file */
            if (strtolower($file->getFilename()) == $filename) {
                return $file->getRealPath();
            }
        }

        return null;
    }
}
