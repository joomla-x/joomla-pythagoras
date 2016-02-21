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

class ResponseSenderMiddleware implements MiddlewareInterface
{
	public function handle(ServerRequestInterface $request, ResponseInterface $response, callable $next)
	{
		$response = $next($request, $response);
		$this->sendStatus($response);
		$this->sendHeaders($response);
		$this->sendBody($response);

		return $response;
	}

	protected function sendStatus(ResponseInterface $response)
	{
		$version = $response->getProtocolVersion();
		$status  = $response->getStatusCode();
		$phrase  = $response->getReasonPhrase();
		header("HTTP/{$version} {$status} {$phrase}");
	}

	protected function sendHeaders(ResponseInterface $response)
	{
		foreach ($response->getHeaders() as $name => $values)
		{
			$this->sendHeader($name, $values);
		}
	}

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
