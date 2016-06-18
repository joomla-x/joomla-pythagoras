<?php
/**
 * Part of the Joomla Framework HTTP Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Http\Mock;

use Joomla\Http\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Middleware implements MiddlewareInterface
{
	/** @var  string */
	private $marker;

	public function __construct($marker)
	{
		$this->marker = $marker;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface      $response
	 * @param callable               $next
	 *
	 * @return ResponseInterface
	 */
	public function handle(ServerRequestInterface $request, ResponseInterface $response, callable $next)
	{
		$response->getBody()->write("<$this->marker]");
		/** @var ResponseInterface $response */
		$response = $next($request, $response);
		$response->getBody()->write("[$this->marker>");

		return $response;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface      $response
	 * @param callable               $next
	 *
	 * @return ResponseInterface
	 */
	public function callableHandler(ServerRequestInterface $request, ResponseInterface $response, callable $next)
	{
		$response->getBody()->write("<$this->marker-alternative]");
		/** @var ResponseInterface $response */
		$response = $next($request, $response);
		$response->getBody()->write("[$this->marker-alternative>");

		return $response;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface      $response
	 * @param callable               $next
	 *
	 * @return ResponseInterface
	 */
	public static function staticHandler(ServerRequestInterface $request, ResponseInterface $response, callable $next)
	{
		$response->getBody()->write("<0-static]");
		/** @var ResponseInterface $response */
		$response = $next($request, $response);
		$response->getBody()->write("[0-static>");

		return $response;
	}
}
