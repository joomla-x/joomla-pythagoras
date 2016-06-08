<?php
/**
 * Part of the Joomla Framework Session Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Session;

/**
 * Session Interface
 *
 * @package  Joomla/Session
 *
 * @since    1.0
 */
interface SessionInterface
{
	/**
	 * Returns the value in the session for the given key.
	 *
	 * @param   string $key The key
	 *
	 * @return  mixed
	 */
	public function get($key);

	/**
	 * Registres the given value for the given key in the registry.
	 *
	 * @param   string $key   The key
	 * @param   mixed  $value The value, must be serializable
	 *
	 * @return  void
	 */
	public function set($key, $value);
}
