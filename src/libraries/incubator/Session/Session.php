<?php
/**
 * Part of the Joomla Framework Session Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
namespace Joomla\Session;

use Aura\Session\SessionFactory;

/**
 * Class JoomlaSession
 *
 * @package Joomla/Session
 *
 * @since   1.0
 */
class Session implements SessionInterface
{
	/** @var \Aura\Session\Session */
	private $session = null;

	/**
	 * Session constructor.
	 *
	 * @param   array  $cookieParams  An array of cookie values, typically $_COOKIE.
	 */
	public function __construct($cookieParams)
	{
		$this->session = (new SessionFactory)->newInstance($cookieParams);
	}

	/**
	 * Get a property from the session bucket
	 *
	 * @param   string  $key  The key
	 *
	 * @return  mixed
	 */
	public function get($key)
	{
		return $this->session->getSegment('Joomla')->get($key);
	}

	/**
	 * Set a property in the session bucket
	 *
	 * @param   string  $key    The key
	 * @param   mixed   $value  The value
	 *
	 * @return  void
	 */
	public function set($key, $value)
	{
		$this->session->getSegment('Joomla')->set($key, $value);
	}
}
