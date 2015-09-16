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
 * The Web Service provider which loads the mailer.
 *
 * @since  4.0
 */
class JApplicationWebMailerprovider implements ServiceProviderInterface
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
		// Setting the callables for the mailer, always returning a new instance
		$container->set('mailer', array($this, 'getMailer'), false, false);
	}

	public function getMailer(Container $container)
	{
		$conf = $this->app->getContainer()->get('config');

		$smtpauth = ($conf->get('smtpauth') == 0) ? null : 1;
		$smtpuser = $conf->get('smtpuser');
		$smtppass = $conf->get('smtppass');
		$smtphost = $conf->get('smtphost');
		$smtpsecure = $conf->get('smtpsecure');
		$smtpport = $conf->get('smtpport');
		$mailfrom = $conf->get('mailfrom');
		$fromname = $conf->get('fromname');
		$mailer = $conf->get('mailer');

		// Create a JMail object
		$mail = new JMail();

		// Set default sender without Reply-to
		$mail->SetFrom(JMailHelper::cleanLine($mailfrom), JMailHelper::cleanLine($fromname), 0);

		// Default mailer is to use PHP's mail function
		switch ($mailer)
		{
			case 'smtp':
				$mail->useSmtp($smtpauth, $smtphost, $smtpuser, $smtppass, $smtpsecure, $smtpport);
				break;

			case 'sendmail':
				$mail->IsSendmail();
				break;

			default:
				$mail->IsMail();
				break;
		}

		return $mail;
	}
}