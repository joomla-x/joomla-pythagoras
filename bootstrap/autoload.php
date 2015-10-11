<?php
/**
 * @package    Joomla
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/*
 * Define the application's minimum supported PHP version as a constant so
 * it can be referenced within the application.
 *
*/
define('JOOMLA_MINIMUM_PHP', '5.5.9');

if (version_compare(PHP_VERSION, JOOMLA_MINIMUM_PHP, '<'))
{
    die('Your host needs to use PHP ' . JOOMLA_MINIMUM_PHP . ' or higher to run this version of Joomla!');
}

/*
 * We need to set this so that the libraries can be loaded, this is quite
 * close to legacy code and should be removed as soon as possible.
 *
 */
define('JPATH_PLATFORM', __DIR__ . '/../libraries');


abstract class JLoader
{
    private static $composer;

    public static function register($class, $path, $force = true)
    {
        self::$composer->addClassMap(array($class => $path));
    }

    public static function setComposerAutoloader()
    {
        self::$composer = require __DIR__ . '/../libraries/vendor/autoload.php';
    }

    public static function import($key, $base = null)
    {
        return true;
    }

    public static function registerPrefix($prefix, $path, $reset = false, $prepend = false)
    {
        return true;
    }
}

/*
 * Register The Composer Auto Loader
 *
 */
JLoader::setComposerAutoloader();


/**
 * Intelligent file importer.
 *
 * @param   string  $path  A dot syntax path.
 *
 * @return  boolean  True on success.
 *
 * @since   11.1
 */
function jimport($path)
{
    return JLoader::import($path);
}
