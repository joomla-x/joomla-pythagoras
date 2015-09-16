<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Service
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Registry\Registry;

/**
 * The Web Service provider which loads the document, etc.
 *
 * @since  4.0
 */
class JApplicationCmsSessionprovider implements ServiceProviderInterface
{
	/**
	 * The application object.
	 *
	 * @var    JApplicationWeb
	 * @since  4.0
	 */
	private $app;

	/**
	 * Public constructor.
	 *
	 * @param   JApplicationWeb  $app  The application object.
	 *
	 * @since   4.0
	 */
	public function __construct(JApplicationCMS $app)
	{
		$this->app = $app;
	}

	public function register(Container $container)
	{
		$container->set('session', array($this, 'getSession'), true, false);
	}

	public function getSession(Container $container)
	{
		$app = $this->app;

		// Generate a session name.
		$name = md5($app->get('secret') . $app->get('session_name', get_class($app)));

		// Calculate the session lifetime.
		$lifetime = (($app->get('lifetime')) ? $app->get('lifetime') * 60 : 900);


		// Initialize the options for JSession.
		$options = array(
			'name'   => $name,
			'expire' => $lifetime
		);

		switch ($app->getClientId())
		{
			case 0:
				if ($app->get('force_ssl') == 2)
				{
					$options['force_ssl'] = true;
				}

				break;

			case 1:
				if ($app->get('force_ssl') >= 1)
				{
					$options['force_ssl'] = true;
				}

				break;
		}

		// Get the Joomla configuration settings
		$handler = $app->get('session_handler', 'none');

		// Config time is in minutes
		$options['expire'] = ($app->get('lifetime')) ? $app->get('lifetime') * 60 : 900;

		$sessionHandler = new JSessionHandlerJoomla($options, $app);
		$session = JSession::getInstance($handler, $options, $sessionHandler);

		if ($session->getState() == 'expired')
		{
			$session->restart();
		}
		$session->initialise($container->get('input'), $container->get('dispatcher'));
		$session->start();

		if ($session->isNew())
		{
			$session->set('registry', new Registry('session'));
			$session->set('user', new JUser);
		}

		// TODO: At some point we need to get away from having session data always in the db.
		$db = JFactory::getDbo();

		// Remove expired sessions from the database.
		$time = time();

		if ($time % 2)
		{
			// The modulus introduces a little entropy, making the flushing less accurate
			// but fires the query less than half the time.
			$query = $db->getQuery(true)
				->delete($db->quoteName('#__session'))
				->where($db->quoteName('time') . ' < ' . $db->quote((int) ($time - $session->getExpire())));

			$db->setQuery($query);
			$db->execute();
		}

		// Get the session handler from the configuration.
		$handler = $app->get('session_handler', 'none');

		if (($handler != 'database' && ($time % 2 || $session->isNew()))
			|| ($handler == 'database' && $session->isNew()))
		{
			$this->checkSession($session);
		}
		return $session;
	}

	/**
	 * Checks the user session.
	 *
	 * If the session record doesn't exist, initialise it.
	 * If session is new, create session variables
	 *
	 * @param JSession $session
	 * @return  void
	 *
	 * @since   4.0
	 * @throws  RuntimeException
	 */
	private function checkSession(JSession $session)
	{
		$db = JFactory::getDbo();
		$user = $session->get('user');

		$query = $db->getQuery(true)
		->select($db->quoteName('session_id'))
		->from($db->quoteName('#__session'))
		->where($db->quoteName('session_id') . ' = ' . $db->quote($session->getId()));

		$db->setQuery($query, 0, 1);
		$exists = $db->loadResult();

		// If the session record doesn't exist initialise it.
		if (!$exists)
		{
			$query->clear();

			if ($session->isNew())
			{
				$query->insert($db->quoteName('#__session'))
				->columns($db->quoteName('session_id') . ', ' . $db->quoteName('client_id') . ', ' . $db->quoteName('time'))
				->values($db->quote($session->getId()) . ', ' . (int) $this->app->getClientId() . ', ' . $db->quote((int) time()));
				$db->setQuery($query);
			}
			else
			{
				$query->insert($db->quoteName('#__session'))
				->columns(
						$db->quoteName('session_id') . ', ' . $db->quoteName('client_id') . ', ' . $db->quoteName('guest') . ', ' .
						$db->quoteName('time') . ', ' . $db->quoteName('userid') . ', ' . $db->quoteName('username')
				)
				->values(
						$db->quote($session->getId()) . ', ' . (int) $this->app->getClientId() . ', ' . (int) $user->get('guest') . ', ' .
						$db->quote((int) $session->get('session.timer.start')) . ', ' . (int) $user->get('id') . ', ' . $db->quote($user->get('username'))
				);

				$db->setQuery($query);
			}

			// If the insert failed, exit the application.
			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				throw new RuntimeException(JText::_('JERROR_SESSION_STARTUP'));
			}
		}
	}
}