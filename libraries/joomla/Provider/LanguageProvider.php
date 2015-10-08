<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Service
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Provider;

defined('JPATH_PLATFORM') or die;

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

/**
 * The Joomla language provider which serves languages.
 *
 * @since  4.0
 */
class LanguageProvider implements ServiceProviderInterface
{
	public function register(Container $container)
	{
		// Setting the callables for the language
		$container->set('JLanguage', array($this, 'getLanguage'));

		// Registering the protected language object
		$container->set('language', array($this, 'getLanguage'), true, false);
		$container->alias('lang', 'language');
	}

	/**
	 * Returns the language based on the config in the container.
	 *
	 * @param Container $container
	 *
	 * @return JDatabaseDriver
	 */
	public function getLanguage(Container $container)
	{
		$conf = $container->get('config');
		$locale = $conf->get('language');
		$debug = $conf->get('debug_lang');
		return new \JLanguage($locale, $debug);
	}
}