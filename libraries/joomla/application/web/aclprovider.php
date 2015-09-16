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
 * The Web Service provider which loads the acl.
 *
 * @since  4.0
 */
class JApplicationWebAclprovider implements ServiceProviderInterface
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
		// Setting the callables for the session
		$container->set('acl', array($this, 'getAcl'), true, false);
	}

	public function getAcl(Container $container)
	{
		return new JAccess();
	}
}