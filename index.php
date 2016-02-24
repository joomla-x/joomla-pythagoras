<?php
/**
 * Part of the Joomla CMS
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

use GuzzleHttp\Psr7\BufferStream;
use Joomla\Http\Middleware\CommandBusMiddleware;
use Joomla\Http\Middleware\ConfigurationMiddleware;
use Joomla\Http\Middleware\ContainerSetupMiddleware;
use Joomla\Http\Application;
use Joomla\Http\Middleware\RendererMiddleware;
use Joomla\Http\Middleware\ResponseSenderMiddleware;
use Joomla\Http\Middleware\RouterMiddleware;
use Joomla\Http\ServerRequestFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

require_once 'libraries/vendor/autoload.php';

$app = new Application(
	[
		new ResponseSenderMiddleware,
		new ContainerSetupMiddleware,
		new ConfigurationMiddleware(__DIR__),
		new RendererMiddleware,
		new RouterMiddleware,
		new CommandBusMiddleware,
		#function (ServerRequestInterface $request, ResponseInterface $response, callable $next) {
		#	$body = new BufferStream();
		#	$body->write('<h1>Welcome to the Prototype!</h1><pre>' . print_r($request->getAttributes(), true) . '</pre>');
		#
		#	return $next($request, $response->withBody($body));
		#},
	]
);

$response = $app->run(ServerRequestFactory::fromGlobals()->withHeader('Accept', 'text/html'));
