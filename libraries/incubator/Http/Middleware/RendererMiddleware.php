<?php
/**
 * Part of the Joomla Framework HTTP Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Http\Middleware;

use GuzzleHttp\Psr7\Stream;
use Joomla\Content\CustomContentTypeInterface;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Joomla\Extension\ExtensionInterface;
use Joomla\Http\MiddlewareInterface;
use Joomla\Renderer\EventDecorator;
use Joomla\Renderer\Factory as RendererFactory;
use Joomla\Renderer\RendererInterface;
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
	/** @var string */
	private $mappingFile;

	/** @var Dispatcher */
	private $dispatcher;

	/** @var  Container */
	private $container;

	/**
	 * RendererMiddleware constructor.
	 *
	 * @param   Dispatcher $dispatcher The event dispatcher
	 * @param   Container  $container  The container
	 */
	public function __construct(Dispatcher $dispatcher, Container $container)
	{
		$this->mappingFile = JPATH_ROOT . '/config/renderer.ini';
		$this->dispatcher  = $dispatcher;
		$this->container   = $container;
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
		$renderer = $this->createRenderer($request);

		$this->registerContentTypes($renderer, $this->container->get('extension_factory')->getExtensions());

		$this->container->set('Renderer', $renderer);

		/** @var ResponseInterface $response */
		$response = $next($request, $response);

		return $response->withBody($this->createStream($renderer));
	}

	/**
	 * Create a renderer matching the request.
	 *
	 * @param ServerRequestInterface $request The Request
	 *
	 * @return RendererInterface The renderer
	 */
	private function createRenderer(ServerRequestInterface $request)
	{
		$acceptHeader = $request->getHeaderLine('Accept');

		if (empty($acceptHeader))
		{
			$acceptHeader = 'text/plain';
		}

		$mapping  = parse_ini_file($this->mappingFile);
		$renderer = (new RendererFactory($mapping))->create($acceptHeader, $this->container);
		$renderer = new EventDecorator($renderer, $this->dispatcher);

		return $renderer;
	}

	/**
	 * Register custom content types with the renderer.
	 *
	 * @param RendererInterface    $renderer   The renderer
	 * @param ExtensionInterface[] $extensions The extensions
	 */
	private function registerContentTypes(RendererInterface $renderer, array $extensions)
	{
		foreach ($extensions as $extension)
		{
			foreach ($extension->getContentTypes() as $contentTypeClass)
			{
				/** @var CustomContentTypeInterface $contentType */
				$contentType = new $contentTypeClass;
				$contentType->register($renderer);
			}
		}
	}

	/**
	 * Create a PSR-7 message stream from the renderer's content.
	 *
	 * @param RendererInterface $renderer The renderer
	 *
	 * @return Stream The PSR-7 message stream
	 */
	private function createStream(RendererInterface $renderer)
	{
		$stream  = fopen('php://temp', 'r+');
		$content = $renderer->getContents();

		if ($content !== '')
		{
			fwrite($stream, $content);
			fseek($stream, 0);
		}

		return new Stream($stream);
	}
}
