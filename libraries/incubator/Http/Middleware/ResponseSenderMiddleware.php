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

/**
 * Sends the response
 *
 * @package  Joomla/HTTP
 *
 * @since    1.0
 */
class ResponseSenderMiddleware implements MiddlewareInterface
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
	public function handle(ServerRequestInterface $request, ResponseInterface $response, callable $next)
	{
		$response = $next($request, $response);
		$this->sendStatus($response);
		$this->sendHeaders($response);
		$this->sendBody($response);

		return $response;
	}

	/**
	 * Send the status
	 *
	 * @param   ResponseInterface $response The response object
	 *
	 * @return  void
	 */
	protected function sendStatus(ResponseInterface $response)
	{
		$version = $response->getProtocolVersion();
		$status  = $response->getStatusCode();
		$phrase  = $response->getReasonPhrase();
		header("HTTP/{$version} {$status} {$phrase}");
	}

	/**
	 * Send the headers
	 *
	 * @param   ResponseInterface $response The response object
	 *
	 * @return  void
	 */
	protected function sendHeaders(ResponseInterface $response)
	{
		foreach ($response->getHeaders() as $name => $values)
		{
			$this->sendHeader($name, $values);
		}
	}

	/**
	 * Send one header
	 *
	 * @param   string  $name    The header tag
	 * @param   array   $values  The values
	 *
	 * @return  void
	 */
	protected function sendHeader($name, $values)
	{
		$name = str_replace('-', ' ', $name);
		$name = ucwords($name);
		$name = str_replace(' ', '-', $name);

		foreach ($values as $value)
		{
			header("{$name}: {$value}", false);
		}
	}

	/**
	 * Send the body
	 *
	 * @param   ResponseInterface $response The response object
	 *
	 * @return  void
	 */
	protected function sendBody(ResponseInterface $response)
	{
		$stream = $response->getBody();

		if ($stream->isSeekable())
		{
			$stream->rewind();

			while (!$stream->eof())
			{
				echo $stream->read(8192);
			}
		}
		else
		{
			echo $stream;
		}
	}
}
