<?php
/**
 * Part of the Joomla Framework HTTP Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Http\Middleware;

use Joomla\Http\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Joomla\Service\CommandBus;

/**
 * Class CommandBusMiddleware
 *
 * @package  Joomla/HTTP
 *
 * @since    1.0
 */
class CommandBusMiddleware implements MiddlewareInterface
{
	/** @var CommandBus  */
	private $commandBus;

	/**
	 * RendererMiddleware constructor.
	 *
	 * @param   CommandBus $commandBus The command bus
	 */
	public function __construct(CommandBus $commandBus)
	{
		$this->commandBus = $commandBus;
	}

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
		$command = $request->getAttribute('command');

		if (empty($command))
		{
			throw new \RuntimeException('No command provided');
		}

		$this->commandBus->handle($command);

		$response = $next($request, $response);

		return $response;
	}
}
