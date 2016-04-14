<?php
/**
 * Part of the Joomla Framework HTTP Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Http;

use Joomla\DI\Container;
use Joomla\Http\Application;
use Joomla\Http\Middleware\ConfigurationMiddleware;
use Joomla\Registry\Registry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use UnitTester;
use Zend\Diactoros\ServerRequest;

class ConfigurationCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    public function ConfigurationIsAddedToTheContainer(UnitTester $I)
    {
    	$container = new Container();
        $app = new Application([
            new ConfigurationMiddleware(__DIR__ . '/data', $container),
            function (ServerRequestInterface $request, ResponseInterface $response, callable $next) use ($I, $container) {
                /** @var Registry $config */
                $config = $container->get('config');
                $I->assertTrue($config instanceof Registry);
                $I->assertEquals('value', $config->get('TEST_VARIABLE'));

                return $next($request, $response);
            }
        ]);

        $request = new ServerRequest();
        $app->run($request);
    }
}
