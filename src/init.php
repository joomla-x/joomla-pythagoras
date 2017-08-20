<?php
/**
 * Part of the Joomla CMS
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\DI\Loader\IniLoader;

/**
 * Create the container
 *
 * @todo    Move this to a ContainerFactory
 *
 * @return  \Joomla\DI\Container
 */
function initContainer()
{
    $container = new \Joomla\DI\Container;
    $container->set('ConfigDirectory', __DIR__);

    (new IniLoader($container))->loadFromFile(__DIR__ . '/config/services.ini');

    if (!defined('JPATH_ROOT')) {
        define('JPATH_ROOT', $container->get('config')->get('JPATH_ROOT', __DIR__));
    }

    return $container;
}
