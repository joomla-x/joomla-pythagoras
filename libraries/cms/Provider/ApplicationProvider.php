<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Service
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Cms\Provider;

defined('JPATH_PLATFORM') or die;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * The CMS Service provider which loads the application.
 *
 * @since  4.0
 */
class ApplicationProvider implements ServiceProviderInterface
{
	public function register(Container $container)
	{
		$container->set('JApplicationSite', array($this, 'getApplicationSite'));
		$container->set('JApplicationAdministrator', array($this, 'getApplicationAdministrator'));
	}

	/**
	 * Creates a JApplicationSite object;
	 *
	 * @param Container $container
	 *
	 * @return JApplicationSite
	 */
	public function getApplicationSite(Container $container)
	{
		$app = new \JApplicationSite($container->get('Input'), $container->get('config'));
		$app->setContainer($container);
		$app->setLanguage($container->get('JLanguage'));

		return $app;
	}

	/**
	 * Creates a JApplicationAdministrator object;
	 *
	 * @param Container $container
	 *
	 * @return JApplicationAdministrator
	 */
	public function getApplicationAdministrator(Container $container)
	{
		$app = new \JApplicationAdministrator($container->get('Input'), $container->get('config'));
		$app->setContainer($container);
		$app->setLanguage($container->get('JLanguage'));

		return $app;
	}
}