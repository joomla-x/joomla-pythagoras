<?php
/**
 * Part of the Joomla Framework HTTP Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Http\Middleware;

use Psr\Container\ContainerInterface;
use Joomla\Event\Dispatcher;
use Joomla\Http\MiddlewareInterface;
use Joomla\Renderer\EventDecorator;
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
 * @since    __DEPLOY_VERSION__
 */
class RendererMiddleware implements MiddlewareInterface
{
	/** @var Dispatcher  */
	private $dispatcher;

	/** @var  ContainerInterface */
	private $container;

	/**
	 * RendererMiddleware constructor.
	 *
	 * @param   Dispatcher         $dispatcher The event dispatcher
	 * @param   ContainerInterface $container  The container
	 */
	public function __construct(Dispatcher $dispatcher, ContainerInterface $container)
	{
		$this->dispatcher = $dispatcher;
		$this->container  = $container;
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

		$mapping  = parse_ini_file(JPATH_ROOT . '/config/renderer.ini');
		$renderer = (new RendererFactory($mapping))->create($acceptHeader, $this->container);

		foreach ($this->container->get('extension_factory')->getExtensions() as $extension)
		{
			foreach ($extension->getContentTypes() as $contentTypeClass)
			{
				(new $contentTypeClass)->register($renderer);
			}
		}

		$renderer = new EventDecorator($renderer, $this->dispatcher);

		$response = $next($request, $response->withBody($renderer));

		return $response;
	}
}
