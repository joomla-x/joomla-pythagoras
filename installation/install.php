<?php
/**
 * Part of the Joomla! Installer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\Cms\Installer\Installer;

define('JPATH_ROOT', realpath(dirname(__DIR__)));

ini_set('date.timezone', 'UTC');

require_once JPATH_ROOT . '/libraries/vendor/autoload.php';

try
{
	$installer = new Installer(JPATH_ROOT . "/data");
	$installer->install(JPATH_ROOT . '/extensions/Article');
	$installer->install(JPATH_ROOT . '/libraries/incubator/Media');
	$installer->install(JPATH_ROOT . '/libraries/incubator/PageBuilder');
	$installer->finish();

	return 0;
}
catch (\Exception $e)
{
	echo "\n" . $e->getMessage() . "\n";

	return 1;
}
