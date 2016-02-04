<?php
/**
 * Part of the Joomla Framework HTTP Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Http;

use Interop\Container\ContainerInterface;
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

    public function ContainerSetupInjectsAnInteropContainer(UnitTester $I)
    {
        $app = new Application([
            new ContainerSetupMiddleware,
            function (ServerRequestInterface $request, ResponseInterface $response, callable $next) use ($I) {
                $I->assertTrue($request->getAttribute('container') instanceof ContainerInterface);

                return $next($request, $response);
            }
        ]);

        $request = new ServerRequest();
        $app->run($request);
    }
}
