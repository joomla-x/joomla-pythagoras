<?php
/**
 * Part of the Joomla Framework HTTP Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Http\Middleware;

use Joomla\Command\CommandInterface;
use Joomla\Http\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class CommandBusMiddleware
 *
 * @package  Joomla/command
 *
 * @since    1.0
 */
class CommandBusMiddleware implements MiddlewareInterface
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
		$commandDetails = $request->getAttribute('command');
		if (is_null($commandDetails))
		{
			throw new \RuntimeException('No command provided');
		}
		$classname = '\\Joomla\\Component\\' . ucfirst($commandDetails['component']) . '\\Command\\' . ucfirst($commandDetails['command']);

		/** @var CommandInterface $command */
		$input   = $request->getAttributes();
		$output  = $response->getBody();
		$command = new $classname();
		$command->execute($input, $output);

		$response = $next($request, $response);

		return $response;
	}
}
