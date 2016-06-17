<?php

namespace Joomla\Tests\Unit\Cms\ServiceProvider;

use Joomla\Cms\ServiceProvider\RequestServiceProvider;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestServiceProviderTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @testdox The RequestServiceProvider implements the
	 * ServiceProviderInterface interface
	 */
	public function testTheTestRequestServiceProviderImplementsTheServiceProviderInterface()
	{
		$this->assertInstanceOf(ServiceProviderInterface::class, new RequestServiceProvider());
	}

	/**
	 * @testdox The RequestServiceProvider adds a RequestInterface to a
	 * container
	 */
	public function testRequestServiceProviderCreatesRequestInterface()
	{
		$container = new Container();

		$service = new RequestServiceProvider();
		$service->register($container);

		$this->assertInstanceOf(ServerRequestInterface::class, $container->get('Request'));
	}

	/**
	 * @testdox The RequestServiceProvider adds an RequestInterface to a
	 * container with an alias
	 */
	public function testRequestServiceProviderCreatesDispatcherWithAlias()
	{
		$container = new Container();

		$service = new RequestServiceProvider();
		$service->register($container, 'unit');

		$this->assertInstanceOf(ServerRequestInterface::class, $container->get('unit'));
	}
}
