<?php
/**
 * Part of the Joomla Framework HTTP Middleware Package Test Suite
 *
 * @copyright  Copyright (C) 2015 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Tests\Unit\Http\Middleware;

use Joomla\Http\Middleware\ContainerSetupMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Joomla\DI\Container;
use Joomla\Event\DispatcherInterface;

class ContainerSetupMiddlewareTest extends \PHPUnit_Framework_TestCase
{

    public function testSetupContainerMiddleware()
    {
    	$request = $this->getMockBuilder(ServerRequestInterface::class)->getMock();
    	$request->expects($this->once())->method('withAttribute')->willReturnSelf()->with(
    		$this->equalTo('container'),
    		$this->callback(function(Container $container)
	    		{
	    			return $container->get('EventDispatcher') instanceof DispatcherInterface;
	    		}
    		)
    	);
    	$response = $this->getMockBuilder(ResponseInterface::class)->getMock();
    	$next = function(ServerRequestInterface $req, ResponseInterface $resp) use ($request, $response)
    	{
    		$this->assertEquals($request, $req);
    		$this->assertEquals($response, $resp);
    	};

        $middleware = new ContainerSetupMiddleware();
        $middleware->handle($request, $response, $next);
    }
}
