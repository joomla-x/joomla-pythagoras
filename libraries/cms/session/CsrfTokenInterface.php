<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Session
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Cms\Session;

defined('JPATH_PLATFORM') or die;

/**
 * Interface CsrfTokenInterface
 *
 * @package  Joomla\Cms\Session
 *
 * @since    4.0
 */
interface CsrfTokenInterface
{
	/**
	 * Get a session token, if a token isn't set yet one will be generated.
	 *
	 * Tokens are used to secure forms from spamming attacks. Once a token
	 * has been generated the system will check the post request to see if
	 * it is present, if not it will invalidate the session.
	 *
	 * @param   boolean  $forceNew  If true, force a new token to be created
	 *
	 * @return  string  The session token
	 */
	public function get($forceNew = false);

	/**
	 * Method to determine if a token exists in the session. If not the
	 * session will be set to expired
	 *
	 * @param   string   $tCheck       Hashed token to be verified
	 * @param   boolean  $forceExpire  If true, expires the session
	 *
	 * @return  boolean
	 */
	public function has($tCheck, $forceExpire = true);

	/**
	 * Checks for a form token in the request.
	 *
	 * Use in conjunction with JHtml::_('form.token') or JSession::getFormToken.
	 *
	 * @param   string  $method  The request method in which to look for the token key.
	 *
	 * @return  boolean  True if found and valid, false otherwise.
	 */
	public function check($method = 'post');

	/**
	 * Checks for a form token in the request, redirects on missing token, or bails out on invalid token.
	 *
	 * @param   string  $method  The request method in which to look for the token key.
	 *
	 * @return  boolean  True if found and valid, false otherwise.
	 *
	 * @see     check()
	 */
	public function guard($method = 'post');

	/**
	 * Create a token-string
	 *
	 * @param   integer  $length  Length of string
	 *
	 * @return  string  Generated token
	 */
	public function create($length = 32);

	/**
	 * Method to determine a hash for anti-spoofing variable names
	 *
	 * @param   boolean  $forceNew  If true, force a new token to be created
	 *
	 * @return  string  Hashed var name
	 */
	public function getVarname($forceNew = false);
}
