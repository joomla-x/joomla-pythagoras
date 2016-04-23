<?php
/**
 * Part of the Joomla Framework HTTP Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Http;

use Joomla\Event\Dispatcher;
use Joomla\Http\Application;
use Joomla\Http\Middleware\RendererMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use UnitTester;
use Zend\Diactoros\ServerRequest;

class RendererCest
{
	public function _before(UnitTester $I)
	{
	}

	public function _after(UnitTester $I)
	{
	}

	public function RendererDefaultsToPlainText(UnitTester $I)
	{
		$app = new Application([
			new RendererMiddleware(new Dispatcher()),
			function (ServerRequestInterface $request, ResponseInterface $response, callable $next) use ($I)
			{
				$body = $response->getBody();

				#$I->assertTrue($body instanceof PlainRenderer);

				return $next($request, $response);
			}
		]);

		$app->run(new ServerRequest());
	}
}
