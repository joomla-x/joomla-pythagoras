<?php
/**
 * Part of the Joomla CMS
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\Http\Middleware\CommandBusMiddleware;
use Joomla\Http\Middleware\ConfigurationMiddleware;
use Joomla\Http\Middleware\ContainerSetupMiddleware;
use Joomla\Http\Application;
use Joomla\Http\Middleware\RendererMiddleware;
use Joomla\Http\ServerRequestFactory;

require_once 'libraries/vendor/autoload.php';

$app = new Application(
	[
		new ContainerSetupMiddleware,
		new ConfigurationMiddleware(dirname(__DIR__)),
		new RendererMiddleware,
		new CommandBusMiddleware,
	]
);

$response = $app->run(ServerRequestFactory::fromGlobals()->withHeader('Accept', 'text/html'));
print_r($response->getBody()->getMetadata());
