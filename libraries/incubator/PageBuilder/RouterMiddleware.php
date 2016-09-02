<?php
/**
 * Part of the Joomla PageBuilder Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\PageBuilder;

use InvalidArgumentException;
use Joomla\DI\Container;
use Joomla\Http\MiddlewareInterface;
use Joomla\ORM\Repository\RepositoryInterface;
use Joomla\PageBuilder\Entity\Page;
use Joomla\Router\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Determines the command to be executed.
 *
 * @package  Joomla/PageBuilder
 *
 * @since    __DEPLOY_VERSION__
 */
class RouterMiddleware implements MiddlewareInterface
{
	/** @var Container */
	private $container;

	/**
	 * RouterMiddleware constructor.
	 *
	 * @param   Container $container The container
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
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
		$attributes = $request->getAttributes();

		if (!isset($attributes['command']))
		{
			try
			{
				/** @var RepositoryInterface $repository */
				$repository = $this->container->get('Repository')->forEntity(Page::class);

				/** @var Page[] $pages */
				$pages = $repository->getAll();

				$router = new Router;

				foreach ($pages as $page)
				{
					$router->get($this->expandUrl($page->url, $page), function () use ($page) {
						return $page;
					});
				}

				$path  = preg_replace('~^/?index.php/?~', '', $request->getUri()->getPath());
				$route = $router->parseRoute($path);
				$page  = $route['controller']();
				$vars  = $route['vars'];

				$command = new DisplayPageCommand($page->id, $vars, $response->getBody(), $this->container);
				$request = $request->withAttribute('command', $command);
				// @todo Emit afterRouting event
			}
			catch (InvalidArgumentException $e)
			{
				// Do nothing
			}
		}

		return $next($request, $response);
	}

	/**
	 * @param $url
	 * @param $page
	 *
	 * @return string
	 */
	private function expandUrl($url, $page)
	{
		if (empty($url))
		{
			return '/';
		}

		while ($url[0] != '/' && !empty($page->parent))
		{
			$page = $page->parent;
			$url  = $page->url . '/' . $url;
		}

		if ($url[0] != '/')
		{
			$url = '/' . $url;
		}

		return $url;
	}
}
