<?php
/**
 * Part of the Joomla CMS
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\DI\Loader\IniLoader;
use Joomla\Http\Application;
use Joomla\Http\Middleware\CommandBusMiddleware;
use Joomla\Http\Middleware\RendererMiddleware;
use Joomla\Http\Middleware\ResponseSenderMiddleware;
use Joomla\Http\Middleware\RouterMiddleware;
use Joomla\Http\ServerRequestFactory;
use Joomla\J3Compatibility\Http\Middleware\RouterMiddleware as LegacyRouterMiddleware;

require_once 'libraries/vendor/autoload.php';
require_once __DIR__ . '/init.php';

$container = initContainer();

$app = new Application(
	[
		new ResponseSenderMiddleware,
		new RendererMiddleware($container->get('dispatcher')),
		new RouterMiddleware,
		new LegacyRouterMiddleware,
		new CommandBusMiddleware,
	]
);

$response = $app->run(ServerRequestFactory::fromGlobals());
