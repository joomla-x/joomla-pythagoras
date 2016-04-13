<?php
/**
 * Part of the Joomla Framework HTTP Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Http\Middleware;

use Joomla\Event\Dispatcher;
use Joomla\Http\MiddlewareInterface;
use Joomla\Renderer\Factory as RendererFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Replaces the response body with a renderer.
 *
 * The renderer is chosen to be suitable for the current request.
 * If the request does not specify a preferred mimetype, `text/plain` is rendered.
 *
 * @package  Joomla/HTTP
 *
 * @since    1.0
 */
class RendererMiddleware implements MiddlewareInterface
{
	/** @var Dispatcher  */
	private $dispatcher;
	
	public function __construct(Dispatcher $dispatcher)
	{
		$this->dispatcher = $dispatcher;
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
		$acceptHeader = $request->getHeaderLine('Accept');

		if (empty($acceptHeader))
		{
			$acceptHeader = 'text/plain';
		}

		$mapping = parse_ini_file(JPATH_ROOT . '/config/renderer.ini');

		$renderer = (new RendererFactory($mapping))->create($acceptHeader);
		$renderer = new \Joomla\Renderer\EventDecorator($renderer, $this->dispatcher);

		$response = $next($request, $response->withBody($renderer));

		return $response;
	}
}
