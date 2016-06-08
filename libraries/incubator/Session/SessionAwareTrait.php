<?php
/**
 * Part of the Joomla Framework Session Package
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Session;

/**
 * Defines the trait for a Session Aware Class.
 *
 * @since  __DEPLOY_VERSION__
 */
trait SessionAwareTrait
{
	/**
	 * Session
	 *
	 * @var    SessionInterface
	 * @since  __DEPLOY_VERSION__
	 */
	private $session;

	/**
	 * Get the session.
	 *
	 * @return  SessionInterface
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getSession()
	{
		return $this->session;
	}

	/**
	 * Set the session to use.
	 *
	 * @param   SessionInterface  $session  The session to use.
	 *
	 * @return  $this
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function setSession(SessionInterface $session)
	{
		$this->session = $session;

		return $this;
	}
}
