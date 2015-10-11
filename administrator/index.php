<?php
/**
 * @package    Joomla.Administrator
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to
 * not error for PHP 5.2 and lower
 */
define('_JEXEC', 1);

/*
 * Register the auto loader
 */
require __DIR__.'/../bootstrap/autoload.php';

define('JAPPLICATIONTYPE', 'administrator');

require __DIR__.'/../bootstrap/defines.php';

require __DIR__.'/../bootstrap/app.php';
