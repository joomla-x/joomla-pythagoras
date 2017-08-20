<?php
/**
 * Part of the Joomla Framework HTTP Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Http;

use Joomla\Http\Application;
use Joomla\Http\Exception\UnsupportedMiddlewareException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use UnitTester;
use Zend\Diactoros\ServerRequest;

class RunnerCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    public function ExecutionIsNestedCorrectly(UnitTester $I)
    {
        $app = new Application([
            new Mock\Middleware('1'),
            new Mock\Middleware('2'),
            new Mock\Middleware('3'),
        ]);

        $request  = new ServerRequest();
        $response = $app->run($request);
        $I->assertEquals('<1]<2]<3][3>[2>[1>', (string)$response->getBody());
    }

    public function ClosuresAreAcceptedAsMiddleware(UnitTester $I)
    {
        $app = new Application([
            function (RequestInterface $request, ResponseInterface $response, callable $next) {
                $response->getBody()->write('<1-Closure]');
                /** @var ResponseInterface $response */
                $response = $next($request, $response);
                $response->getBody()->write('[1-Closure>');

                return $response;
            },
            new Mock\Middleware('2'),
        ]);

        $request  = new ServerRequest();
        $response = $app->run($request);
        $I->assertEquals('<1-Closure]<2][2>[1-Closure>', (string)$response->getBody());
    }

    public function CallablesAreAcceptedAsMiddleware(UnitTester $I)
    {
        $app = new Application([
            [new Mock\Middleware(1), 'callableHandler'],
            new Mock\Middleware('2'),
        ]);

        $request  = new ServerRequest();
        $response = $app->run($request);
        $I->assertEquals('<1-alternative]<2][2>[1-alternative>', (string)$response->getBody());
    }

    public function StaticCallablesAreAcceptedAsMiddleware(UnitTester $I)
    {
        $app = new Application([
            [Mock\Middleware::class, 'staticHandler'],
            new Mock\Middleware('2'),
        ]);

        $request  = new ServerRequest();
        $response = $app->run($request);
        $I->assertEquals('<0-static]<2][2>[0-static>', (string)$response->getBody());
    }

    public function ExternalMiddlewareIsAccepted(UnitTester $I)
    {
        $tokenStorage = [];

        $app = new Application([
            new \Slim\Csrf\Guard('token', $tokenStorage),
            function (ServerRequestInterface $request, ResponseInterface $response, callable $next) use ($I) {
                $I->assertNotEmpty($request->getAttribute('token_name'));
                $I->assertNotEmpty($request->getAttribute('token_value'));

                return $next($request, $response);
            },
        ]);

        $request = new ServerRequest();
        $app->run($request);

        $I->assertGreaterThan(0, count($tokenStorage));
    }

    /**
     * UnsupportedMiddlewareException
     */
    public function UnsupportedMiddlewareThrowsException(UnitTester $I)
    {
        $app = new Application([
            new \stdClass,
        ]);

        $request = new ServerRequest();
        try {
            $app->run($request);
            $I->fail('Expected UnsupportedMiddlewareException was not thrown');
        } catch (\Exception $e) {
            $I->assertEquals(UnsupportedMiddlewareException::class, get_class($e));
        }
    }
}
