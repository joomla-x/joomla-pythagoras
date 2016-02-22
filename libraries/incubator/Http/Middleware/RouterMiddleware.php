<?php
/**
 * Part of the Joomla Framework HTTP Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Http\Middleware;

use Joomla\Http\MiddlewareInterface;
use Joomla\Registry\Registry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @package  Joomla/renderer
 *
 * @since    1.0
 */
class RouterMiddleware implements MiddlewareInterface
{
	/**
	 * Execute the middleware. Don't call this method directly; it is used by the `Application` internally.
	 *
	 * @internal
	 *
	 * @param   ServerRequestInterface $request  The request object
	 * @param   ResponseInterface      $response The response object
	 * @param   callable               $next     The next middleware handler
	 *
	 * @return  ResponseInterface
	 */
	public function handle(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
	{
		switch (strtoupper($request->getMethod()))
		{
			case 'GET':
				$params = new Registry($request->getQueryParams());
				break;

			case 'POST':
			default:
				$params = new Registry($request->getAttributes());
				break;
		}

		$command  = [
			'component' => $params->get('option', 'error'),
			'command'   => $params->get('task', 'display'),
			'id'        => $params->get('id', null),
		];
		$request  = $request->withAttribute('command', $command);
		$response = $next($request, $response);

		return $response;
	}
}
