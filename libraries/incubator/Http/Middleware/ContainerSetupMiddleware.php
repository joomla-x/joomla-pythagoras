<?php
/**
 * Part of the Joomla Framework HTTP Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Http\Middleware;

use Interop\Container\ContainerInterface;
use Joomla\DI\Container;
use Joomla\Http\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Adds a Dependency Injection Container.
 *
 * An [Interop/Container](https://github.com/container-interop/container-interop/blob/master/docs/ContainerInterface.md)
 * compatible container is created and added to the request as attribute `container`.
 * If you want to access the container, your middleware **MUST** be located **after** `ContainerSetupMiddleware`
 * in the `Application` constructor.
 *
 * @example
 * ```php
 * <?php
 *
 * namespace Vendor;
 *
 * use Joomla\Http\MiddlewareInterface;
 * use Psr\Http\Message\ResponseInterface;
 * use Psr\Http\Message\ServerRequestInterface;
 *
 * class MyMiddleware implements MiddlewareInterface
 * {
 *     public function handle(ServerRequestInterface $request, ResponseInterface $response, callable $next)
 *     {
 *         // ...
 *
 *         // Retrieve the container
 *         $container = $request->getAttribute('container');
 *
 *         // ...
 *     }
 * }
 * ```
 *
 * @package  Joomla/HTTP
 * @see      [Interop/Container](https://github.com/container-interop/container-interop)
 *
 * @since    1.0
 */
class ContainerSetupMiddleware implements MiddlewareInterface
{
	/** @var Container */
	private $container;
	
	public function __construct(ContainerInterface $container)
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
	public function handle(ServerRequestInterface $request, ResponseInterface $response, callable $next)
	{
		$services = parse_ini_file('config/services.ini', true);
		print_r($services);
		foreach ($services['provider'] as $alias => $service)
		{
			$this->container->registerServiceProvider(new $service, $alias);
		}

		$response = $next(
			$request->withAttribute('container', $this->container),
			$response
		);

		return $response;
	}
}
