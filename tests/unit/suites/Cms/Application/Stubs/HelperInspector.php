<?php
/**
 * @package     Joomla.UnitTest
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cms\Tests\Application\Stubs;

use Joomla\Cms\Application\Helper;

/**
 * Test class for Helper
 *
 * @package     Joomla.UnitTest
 * @subpackage  Application
 * @since       3.4
 */
class HelperInspector extends Helper
{
	/**
	 * Method to get the current application data
	 *
	 * @return  array  The array of application data objects.
	 *
	 * @since   3.4
	 */
	public static function get()
	{
		return self::$_clients;
	}

	/**
	 * Set the application data.
	 *
	 * @param   string  $apps  The app to set.
	 *
	 * @return  void
	 *
	 * @since   3.4
	 */
	public static function set($apps)
	{
		self::$_clients = $apps;
	}
}
