<?php
/**
 * @package    Joomla.Installation
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Define the application's minimum supported PHP version as a constant so it can be referenced within the application.
 */
define('JOOMLA_MINIMUM_PHP', '5.3.10');

if (version_compare(PHP_VERSION, JOOMLA_MINIMUM_PHP, '<'))
{
	die('Your host needs to use PHP ' . JOOMLA_MINIMUM_PHP . ' or higher to run this version of Joomla!');
}

/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
define('_JEXEC', 1);

// Bootstrap the application
require_once dirname(__FILE__) . '/application/bootstrap.php';

$container = new Joomla\DI\Container();
$container->registerServiceProvider(new Joomla\Provider\InputProvider());
$container->registerServiceProvider(new Joomla\Provider\LanguageProvider());
$container->registerServiceProvider(new Joomla\Provider\DatabaseProvider());
$container->registerServiceProvider(new Joomla\Cms\Provider\ConfigurationProvider());
$container->registerServiceProvider(new Joomla\Provider\SessionProvider());
$container->registerServiceProvider(new Joomla\Cms\Provider\ApplicationProvider());

$container->set('JDocument',
	function (Joomla\DI\Container $container)
	{
		$lang = $container->get('language');
		$attributes = array(
				'charset' => 'utf-8',
				'lineend' => 'unix',
				'tab' => '  ',
				'language' => $lang->getTag(),
				'direction' => $lang->isRtl() ? 'rtl' : 'ltr',
		);

		return \JDocument::getInstance($container->get('input')->getWord('format', 'html'), $attributes);
	}
);

// Get the application
$app = $container->get('InstallationApplicationWeb');

// Execute the application
$app->execute();
