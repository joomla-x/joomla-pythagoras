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
 * Class CsrfToken
 *
<<<<<<< HEAD
 * @package  Joomla\Cms\Session
 *
 * @since    4.0
=======
 * @package Joomla\Cms\Session
 *
 * @since 4.0
>>>>>>> 40eca02... Fixed CS
 */
class CsrfToken implements CsrfTokenInterface
{
	/** @var \JSession The session object */
	private $session;

	/**
	 * Constructor
	 *
	 * @param   \JSession  $session  The session object
	 */
	public function __construct(\JSession $session)
	{
		$this->session = $session;
	}

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
	public function get($forceNew = false)
	{
		$token = $this->session->get('session.token');

		if ($token === null || $forceNew)
		{
			$token = $this->create(12);
			$this->session->set('session.token', $token);
		}

		return $token;
	}

	/**
	 * Method to determine if a token exists in the session. If not the
	 * session will be set to expired
	 *
	 * @param   string   $tCheck       Hashed token to be verified
	 * @param   boolean  $forceExpire  If true, expires the session
	 *
	 * @return  boolean
	 */
	public function has($tCheck, $forceExpire = true)
	{
		$tStored = $this->session->get('session.token');

		if (($tStored !== $tCheck))
		{
			if ($forceExpire)
			{
				$this->_state = 'expired';
			}

			return false;
		}

		return true;
	}

	/**
	 * Checks for a form token in the request.
	 *
	 * Use in conjunction with JHtml::_('form.token') or JSession::getFormToken.
	 *
	 * @param   string  $method  The request method in which to look for the token key.
	 *
	 * @return  boolean  True if found and valid, false otherwise.
	 */
	public function check($method = 'post')
	{
		$token = self::getVarname();
		$app   = \JFactory::getApplication();

		if (!$app->input->$method->get($token, '', 'alnum'))
		{
			if ($this->session->isNew())
			{
				throw new NoTokenException;
			}

			return false;
		}

		return true;
	}

	/**
	 * Checks for a form token in the request, redirects on missing token, or bails out on invalid token.
	 *
	 * @param   string  $method  The request method in which to look for the token key.
	 *
	 * @return  boolean  True if found and valid, false otherwise.
	 *
	 * @see     check()
	 */
	public function guard($method = 'post')
	{
		try
		{
			if (!$this->check($method))
			{
				\jexit(\JText::_('JINVALID_TOKEN'));
			}
		}
		catch (\Joomla\Cms\Session\NoTokenException $e)
		{
			$app = \JFactory::getApplication();
			$app->enqueueMessage(\JText::_('JLIB_ENVIRONMENT_SESSION_EXPIRED'), 'warning');
			$app->redirect(\JRoute::_('index.php'));
		}
	}

	/**
	 * Create a token-string
	 *
	 * @param   integer  $length  Length of string
	 *
	 * @return  string  Generated token
	 */
	public function create($length = 32)
	{
		static $chars = '0123456789abcdef';
		$max   = strlen($chars) - 1;
		$token = '';
		$name  = session_name();

		for ($i = 0; $i < $length; ++$i)
		{
			$token .= $chars[(rand(0, $max))];
		}

		return md5($token . $name);
	}

	/**
	 * Method to determine a hash for anti-spoofing variable names
	 *
	 * @param   boolean  $forceNew  If true, force a new token to be created
	 *
	 * @return  string  Hashed var name
	 */
	public function getVarname($forceNew = false)
	{
		$user = \JFactory::getUser();

		return md5(
			\JFactory::getApplication()->get('secret')
			. $user->get('id', 0)
			. $this->get($forceNew)
		);
	}
}
