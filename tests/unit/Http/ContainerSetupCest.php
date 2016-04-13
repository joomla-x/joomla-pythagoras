<?php
/**
 * Part of the Joomla Framework HTTP Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Http;

use Interop\Container\ContainerInterface;
use Joomla\DI\Container;
use Joomla\Event\DispatcherInterface;
use Joomla\Http\Application;
use Joomla\Http\Middleware\ContainerSetupMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use UnitTester;
use Zend\Diactoros\ServerRequest;

class ContainerSetupCest
{
	public function _before(UnitTester $I)
	{
	}

	public function _after(UnitTester $I)
	{
	}

	/**
	 * @testdox  ContainerSetup injects an InteropContainer
	 */
	public function ContainerSetupInjectsAnInteropContainer(UnitTester $I)
	{
		$app = new Application([
			new ContainerSetupMiddleware(new Container()),
			function (ServerRequestInterface $request, ResponseInterface $response, callable $next) use ($I)
			{
				$I->assertTrue($request->getAttribute('container') instanceof ContainerInterface);

				return $next($request, $response);
			}
		]);

		$request = new ServerRequest();
		$app->run($request);
	}

	/**
	 * @testdox  Container provides an EventDispatcher
	 */
	public function ContainerProvidesAnEventDispatcher(UnitTester $I)
	{
		$app = new Application([
			new ContainerSetupMiddleware(new Container()),
			function (ServerRequestInterface $request, ResponseInterface $response, callable $next) use ($I)
			{
				$I->assertTrue($request->getAttribute('container')->get('EventDispatcher') instanceof DispatcherInterface);

				return $next($request, $response);
			}
		]);

		$request = new ServerRequest();
		$app->run($request);
	}

	/**
	 * @testdox  Container provides 'dispatcher' alias for EventDispatcher
	 */
	public function ContainerEventDispatcherAlias(UnitTester $I)
	{
		$app = new Application([
			new ContainerSetupMiddleware(new Container()),
			function (ServerRequestInterface $request, ResponseInterface $response, callable $next) use ($I)
			{
				$I->assertTrue($request->getAttribute('container')->get('dispatcher') instanceof DispatcherInterface);

				return $next($request, $response);
			}
		]);

		$request = new ServerRequest();
		$app->run($request);
	}
}
