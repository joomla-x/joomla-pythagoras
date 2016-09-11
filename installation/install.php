<?php
/**
 * Part of the Joomla! Installer Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\Cms\Installer\Installer;

ini_set('date.timezone', 'UTC');

require_once $root . '/libraries/vendor/autoload.php';

try
{
	$root      = realpath(dirname(__DIR__));
	$installer = new Installer($root . "/data");
	$installer->install($root . '/extensions/Article');
	$installer->install($root . '/libraries/incubator/Media');
	$installer->install($root . '/libraries/incubator/PageBuilder');
	$installer->finish();

	return 0;
}
catch (\Exception $e)
{
	echo "\n" . $e->getMessage() . "\n";

	return 1;
}
