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
class JApplicationWebService implements ServiceProviderInterface
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
	public function __construct(JApplicationWeb $app)
	{
		$this->app = $app;
	}

	public function register(Container $container)
	{
		// Setting the callables for the keys which are needed in a web app
		$container->set('document', array($this, 'getDocument'), false, true);
		$container->set('session', array($this, 'getSession'), true, false);
		$container->set('language', array($this, 'getLanguage'), true, false);
	}

	public function getLanguage(Container $container)
	{
		$conf = $container->get('config');
		$locale = $conf->get('language');
		$debug = $conf->get('debug_lang');
		return JLanguage::getInstance($locale, $debug);
	}

	public function getSession(Container $container)
	{
		$app = $this->app;

		// Generate a session name.
		$name = md5($app->get('secret') . $app->get('session_name', get_class($app)));

		// Calculate the session lifetime.
		$lifetime = (($app->get('sess_lifetime')) ? $app->get('sess_lifetime') * 60 : 900);

		// Get the session handler from the configuration.
		$handler = $app->get('sess_handler', 'none');

		// Initialize the options for JSession.
		$options = array(
				'name' => $name,
				'expire' => $lifetime,
				'force_ssl' => $app->get('force_ssl')
		);

		// Instantiate the session object.
		$session = JSession::getInstance($handler, $options);
		$session->initialise($container->get('input'), $app->dispatcher);

		if ($session->getState() == 'expired')
		{
			$session->restart();
		}
		else
		{
			$session->start();
		}

		if ($session->isNew())
		{
			$session->set('registry', new Registry('session'));
			$session->set('user', new JUser);
		}

		return $session;
	}
	public function getDocument(Container $container)
	{
		$lang = $container->get('language');

		$input = $container->get('input');
		$type = $input->get('format', 'html', 'word');

		$version = new JVersion;

		$attributes = array(
				'charset' => 'utf-8',
				'lineend' => 'unix',
				'tab' => '  ',
				'language' => $lang->getTag(),
				'direction' => $lang->isRtl() ? 'rtl' : 'ltr',
				'mediaversion' => $version->getMediaVersion()
		);
		return JDocument::getInstance($type, $attributes);
	}
}